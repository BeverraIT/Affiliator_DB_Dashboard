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

    private function ingat(string $nama, \Closure $fn)
    {
        return Cache::remember('kora-boost:' . $nama, 300, $fn);      // KORA memperbarui tiap 30 menit
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

            $target = null;
            $ada = (int) $this->db()->selectOne("SELECT COUNT(*) AS n FROM boost_produk WHERE boost = ? AND product_id <> ''", [$k->kode])->n;
            if ($ada) {
                $target = $this->db()->selectOne("SELECT COUNT(*) AS sampel, COALESCE(SUM(dibuat),0) AS video,
                    COALESCE(SUM(video_laku > 0),0) AS laku, COALESCE(SUM(gmv_sejak_mulai),0) AS gmv
                    FROM v_kinerja_sampel WHERE approved_at >= ? AND product_id IN
                      (SELECT product_id FROM boost_produk WHERE boost = ? AND product_id <> '')", [$k->mulai, $k->kode]);
            }
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
            foreach ($this->db()->select("SELECT p.sku, COUNT(v.approval_id) AS sampel, COALESCE(SUM(v.dibuat),0) AS video,
                    MAX(p.product_id = '') AS belum_peta
                FROM boost_produk p LEFT JOIN v_kinerja_sampel v ON v.product_id = p.product_id AND v.approved_at >= ?
                WHERE p.boost = ? GROUP BY p.sku", [$k->mulai, $k->kode]) as $r) {
                $semua[$r->sku] = $r;
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
        return ['total' => $total, 'baris' => $baris];
    }
}
