<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Data halaman KORA › Boost (25 Nov 2026). Hanya SELECT ke DB creator_agent (koneksi 'kora').
 *
 * KORA menyalin hasil undangan + status tiap kreator ke tabel boost / boost_produk / boost_kreator tiap 30 menit
 * (creator-agent: src/creator_agent/boost.py, migrations/020_boost.sql). Status dihitung di KORA:
 * belum_dikirim → diundang → mengajukan → deal → acc → video → laku (plus gagal_kirim, tidak_lanjut, ditangani_tim).
 * Progres target video = semua sampel produk boost sejak kampanye mulai (sama dengan laporan WA "🎯 PRODUK FOKUS").
 */
class KoraBoost
{
    public const STATUS = [
        'laku'          => ['Laku', 'b-deal'],
        'video'         => ['Video dibuat', 'b-deal'],
        'acc'           => ['Sampel di-ACC', 'b-sampel'],
        'deal'          => ['DEAL, tunggu approve', 'b-sampel'],
        'mengajukan'    => ['Mengajukan', 'b-tim'],
        'ditangani_tim' => ['Ditangani tim', 'b-tim'],
        'tidak_lanjut'  => ['Tidak lanjut', 'b-kreator'],
        'diundang'      => ['Diundang', 'b-kreator'],
        'gagal_kirim'   => ['Gagal kirim', 'b-kreator'],
        'belum_dikirim' => ['Belum dikirim', 'b-kreator'],
    ];

    private const MAJU = ['mengajukan', 'deal', 'ditangani_tim', 'tidak_lanjut', 'acc', 'video', 'laku'];

    private function db()
    {
        return DB::connection('kora');
    }

    private function ingat(string $nama, \Closure $fn, int $detik = 300)
    {
        return Cache::remember('kora-boost:' . $nama, $detik, $fn);
    }

    /**
     * Semua sampel produk boost sejak kampanye mulai, per product_id. Membaca view v_kinerja_sampel (±4 detik),
     * jadi satu query saja dan di-cache 30 menit (= jadwal sinkron KORA). -> [product_id => {sampel, video, laku, gmv}]
     */
    private function sampelProduk(object $k): array
    {
        return $this->ingat('sampel:' . $k->kode, function () use ($k) {
            $hasil = [];
            foreach ($this->db()->select("SELECT product_id, COUNT(*) AS sampel, COALESCE(SUM(dibuat),0) AS video,
                    COALESCE(SUM(video_laku > 0),0) AS laku, COALESCE(SUM(gmv_sejak_mulai),0) AS gmv
                FROM v_kinerja_sampel WHERE approved_at >= ? GROUP BY product_id", [$k->mulai]) as $r) {
                $hasil[(string) $r->product_id] = $r;
            }
            return $hasil;
        }, 1800);
    }

    /** [sku => [product_id, ...]] produk boost ('' = belum terpetakan). */
    private function petaProduk(object $k): array
    {
        $peta = [];
        foreach ($this->db()->select('SELECT sku, product_id FROM boost_produk WHERE boost = ?', [$k->kode]) as $r) {
            $peta[$r->sku][] = (string) $r->product_id;
        }
        return $peta;
    }

    /** Jumlahkan sampel untuk sekumpulan product_id. */
    private function jumlahSampel(array $pids, array $sampel): object
    {
        $t = ['sampel' => 0, 'video' => 0, 'laku' => 0, 'gmv' => 0.0];
        foreach (array_unique($pids) as $pid) {
            if ($pid !== '' && isset($sampel[$pid])) {
                foreach ($t as $kunci => $v) {
                    $t[$kunci] = $v + $sampel[$pid]->$kunci;
                }
            }
        }
        return (object) $t;
    }

    /** Kampanye, terbaru dulu. */
    public function kampanye(): array
    {
        return $this->ingat('kampanye', fn () => $this->db()->select(
            'SELECT kode, nama, mulai, sampai, target_video, komisi, updated_at FROM boost ORDER BY mulai DESC'));
    }

    /**
     * KPI + corong satu kampanye.
     * -> ['status' => [status => n], 'corong' => [...], 'total' => {...}, 'target' => {sampel, video, laku}|null]
     */
    public function ringkasan(object $k): array
    {
        return $this->ingat('ringkas:' . $k->kode, function () use ($k) {
            $status = [];
            foreach ($this->db()->select('SELECT status, COUNT(*) AS n FROM boost_kreator WHERE boost = ? GROUP BY status', [$k->kode]) as $r) {
                $status[$r->status] = (int) $r->n;
            }
            $jumlah = fn (array $s) => array_sum(array_intersect_key($status, array_flip($s)));
            $diundang = $jumlah(array_merge(['diundang'], self::MAJU));
            $corong = [
                ['Diundang', $diundang],
                ['Mengajukan sampel', $jumlah(self::MAJU)],
                ['Sampel di-ACC', $jumlah(['acc', 'video', 'laku'])],
                ['Membuat video', $jumlah(['video', 'laku'])],
                ['Videonya laku', $jumlah(['laku'])],
            ];
            $total = $this->db()->selectOne('SELECT COUNT(*) AS kreator, COALESCE(SUM(sampel),0) AS sampel,
                COALESCE(SUM(video),0) AS video, COALESCE(SUM(gmv),0) AS gmv, COALESCE(SUM(orders),0) AS orders,
                MAX(updated_at) AS diperbarui FROM boost_kreator WHERE boost = ?', [$k->kode]);

            $pids = array_filter(array_merge([], ...array_values($this->petaProduk($k))), fn ($p) => $p !== '');
            $target = $pids ? $this->jumlahSampel($pids, $this->sampelProduk($k)) : null;
            return ['status' => $status, 'corong' => $corong, 'total' => $total, 'target' => $target];
        });
    }

    /** Per produk: undangan & hasil boost, plus semua sampel produk itu (bukan hanya dari undangan). */
    public function perProduk(object $k): array
    {
        return $this->ingat('produk:' . $k->kode, function () use ($k) {
            $maju = "'" . implode("','", self::MAJU) . "'";
            $baris = $this->db()->select("SELECT COALESCE(sku, product_title, '-') AS sku, MAX(product_title) AS judul,
                    SUM(kirim = 'antre') AS antre,
                    SUM(status IN ('diundang', {$maju})) AS diundang,
                    SUM(status IN ({$maju})) AS mengajukan,
                    SUM(status IN ('acc','video','laku')) AS acc,
                    COALESCE(SUM(video),0) AS video, COALESCE(SUM(gmv),0) AS gmv
                FROM boost_kreator WHERE boost = ? GROUP BY COALESCE(sku, product_title, '-')", [$k->kode]);
            $semua = [];
            $sampel = null;
            foreach ($this->petaProduk($k) as $sku => $pids) {
                $sampel ??= $this->sampelProduk($k);
                $semua[$sku] = $this->jumlahSampel($pids, $sampel);
                $semua[$sku]->belum_peta = in_array('', $pids, true);
            }
            $hasil = [];
            foreach ($baris as $r) {
                $s = $semua[$r->sku] ?? null;
                $r->semua_sampel = $s ? (int) $s->sampel : null;
                $r->semua_video  = $s ? (int) $s->video : null;
                $hasil[$r->sku] = $r;
                unset($semua[$r->sku]);
            }
            foreach ($semua as $sku => $s) {          // produk boost tanpa kandidat undangan
                $hasil[$sku] = (object) ['sku' => $sku, 'judul' => null, 'antre' => 0, 'diundang' => 0, 'mengajukan' => 0,
                    'acc' => 0, 'video' => 0, 'gmv' => 0, 'semua_sampel' => (int) $s->sampel, 'semua_video' => (int) $s->video,
                    'belum_peta' => (bool) $s->belum_peta];
            }
            uasort($hasil, fn ($a, $b) => [$b->semua_video ?? $b->video, $b->diundang] <=> [$a->semua_video ?? $a->video, $a->diundang]);
            return array_values($hasil);
        });
    }

    /** SKU boost yang belum terhubung ke produk TikTok (tim perlu memetakan di dashboard). */
    public function belumTerpetakan(object $k): array
    {
        return array_map(fn ($r) => $r->sku, $this->db()->select(
            "SELECT sku FROM boost_produk WHERE boost = ? AND product_id = '' ORDER BY sku", [$k->kode]));
    }

    /** Daftar kreator, urut kemajuan lalu tanggal undang. -> ['total' => int, 'baris' => [...]] */
    public function daftar(object $k, array $f, int $page, int $perPage = 25): array
    {
        $where = 'boost = ?';
        $p = [$k->kode];
        if (!empty($f['toko'])) {
            $where .= ' AND store_slug = ?';
            $p[] = $f['toko'];
        }
        if (!empty($f['sku'])) {
            $where .= ' AND COALESCE(sku, product_title) = ?';
            $p[] = $f['sku'];
        }
        if (!empty($f['status']) && isset(self::STATUS[$f['status']])) {
            $where .= ' AND status = ?';
            $p[] = $f['status'];
        } elseif (empty($f['status'])) {
            $where .= " AND status <> 'belum_dikirim'";      // bawaan: yang sudah dikirim saja
        }
        if (!empty($f['cari'])) {
            $where .= ' AND username LIKE ?';
            $p[] = '%' . ltrim(trim($f['cari']), '@') . '%';
        }
        $urut = "FIELD(status, '" . implode("','", array_keys(self::STATUS)) . "')";
        $total = (int) $this->db()->selectOne("SELECT COUNT(*) AS n FROM boost_kreator WHERE {$where}", $p)->n;
        $baris = $this->db()->select("SELECT * FROM boost_kreator WHERE {$where}
            ORDER BY {$urut}, diundang_at DESC, gmv_sebelum DESC LIMIT ? OFFSET ?",
            array_merge($p, [$perPage, ($page - 1) * $perPage]));
        $this->tambahKeterangan($baris);
        return ['total' => $total, 'baris' => $baris];
    }

    public const KETERANGAN = [
        'ditolak_tim'            => 'Ditolak tim (NO)',
        'ditolak'                => 'Gagal screening',
        'blacklist'              => 'Blacklist',
        'kedaluwarsa'            => 'Kedaluwarsa, tidak diproses',
        'batal'                  => 'Dibatalkan',
        'menunggu_tim'           => 'Menunggu keputusan tim',
        'siap_negosiasi'         => 'Siap negosiasi',
        'nego_menunggu_tim'      => 'Nego: menunggu tim',
        'nego_menunggu_kreator'  => 'Nego: menunggu kreator',
        'ditangani_tim'          => 'Diproses tim',
        'diproses_di_luar_agent' => 'Diproses tim di luar KORA',
    ];

    /**
     * Keterangan di bawah status: alasan dari state pengajuan, dan produk yang diajukan kalau berbeda dari produk
     * yang di-boost (dibaca dari sample_requests.products). ->ket (string|null), ->produk_lain (string|null).
     */
    private function tambahKeterangan(array $baris): void
    {
        $ids = array_values(array_filter(array_map(fn ($r) => $r->request_id ?? null, $baris)));
        $req = [];
        if ($ids) {
            $tanda = implode(',', array_fill(0, count($ids), '?'));
            foreach ($this->db()->select("SELECT id, products FROM sample_requests WHERE id IN ({$tanda})", $ids) as $r) {
                $req[$r->id] = json_decode((string) $r->products, true) ?: [];
            }
        }
        foreach ($baris as $r) {
            $r->ket = self::KETERANGAN[$r->request_state ?? ''] ?? null;
            $r->produk_lain = null;
            $produk = $req[$r->request_id ?? 0] ?? [];
            if ($produk && $r->product_id) {
                $ids = array_map(fn ($p) => (string) ($p['product_id'] ?? ''), $produk);
                if (!in_array((string) $r->product_id, $ids, true)) {
                    $p = $produk[0];
                    $nama = trim(($p['sku_desc'] ?? '') !== '' ? $p['sku_desc'] : ($p['product_title'] ?? ''));
                    $judul = \Illuminate\Support\Str::limit((string) ($p['product_title'] ?? ''), 40);
                    $r->produk_lain = trim($judul . ($nama && $nama !== ($p['product_title'] ?? '') ? " ({$nama})" : ''))
                        . (count($produk) > 1 ? ' +' . (count($produk) - 1) : '');
                }
            }
        }
    }
}
