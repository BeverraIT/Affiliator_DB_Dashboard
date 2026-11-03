<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Data halaman KORA. Hanya SELECT ke DB creator_agent (koneksi 'kora'), semua hasil di-cache:
 * KORA memperbarui datanya per putaran (±60 menit), jadi query view yang berat tidak perlu jalan tiap klik.
 * Definisi angka sama dengan laporan KORA (creator-agent: laporan_kinerja.py, ringkasan.py).
 */
class KoraStats
{
    public const TOKO = [
        'yarra'   => 'Yarra',
        'caramel' => 'Caramel',
        'minzo'   => 'Minzo',
        'keren'   => 'Topi Keren',
        'kece'    => 'Topi Kece',
        'nomide'  => 'Nomide',
    ];

    public const KELOMPOK = [
        'kora_penuh'   => 'KORA penuh',
        'nego_kora'    => 'Nego KORA, approve tim',
        'tim'          => 'Ditangani tim',
        'override_tim' => 'Tim approve yang ditolak KORA',
        'luar_kora'    => 'Di luar KORA',
    ];

    public const ANTRIAN = [
        'menunggu_tim'          => 'Menunggu keputusan tim',
        'nego_menunggu_tim'     => 'Nego: menunggu tim',
        'deal_tunggu_approve'   => 'Deal, menunggu approve',
        'nego_menunggu_kreator' => 'Nego: menunggu kreator',
    ];

    // Sama dengan laporan_kinerja.py: sampel "matang" 21–90 hari, video lengkap dinilai mulai 40 hari.
    public const UMUR_MIN = 21;
    public const UMUR_MAKS = 90;
    public const UMUR_LENGKAP = 40;

    private const TAWARAN = "EXISTS (SELECT 1 FROM messages m WHERE m.request_id = r.id
                             AND m.jenis = 'tawaran_awal' AND m.status = 'sent')";

    public function __construct(private ?string $toko = null)
    {
        $this->toko = array_key_exists((string) $toko, self::TOKO) ? $toko : null;
    }

    private function db()
    {
        return DB::connection('kora');
    }

    private function key(string $nama, ...$bagian): string
    {
        return 'kora:' . $nama . ':' . ($this->toko ?? 'semua') . ':' . implode(':', $bagian);
    }

    /** Filter toko untuk kolom store_slug (alias tabel opsional). */
    private function whereToko(string $kolom = 'store_slug'): array
    {
        return $this->toko ? [" AND {$kolom} = ?", [$this->toko]] : ['', []];
    }

    public function ringkasan(Carbon $dari, Carbon $sampai): array
    {
        $awal  = $dari->copy()->startOfDay();
        $akhir = $sampai->copy()->addDay()->startOfDay();

        return Cache::remember($this->key('ringkasan', $awal->format('Ymd'), $akhir->format('Ymd')), 600, function () use ($awal, $akhir) {
            [$toko, $p] = $this->whereToko('r.store_slug');
            $rentang = [$awal, $akhir];

            $f = $this->db()->selectOne("
                SELECT COUNT(*) AS dicek,
                       COUNT(DISTINCT r.username) AS kreator,
                       SUM(r.screening_result IN ('gagal', 'blacklist')) AS ditolak,
                       SUM(r.screening_result IN ('lolos', 'kreator_bersyarat', 'big_creator_review', 'data_kurang')) AS lolos,
                       SUM(" . self::TAWARAN . ") AS ditawari,
                       SUM(r.state IN ('deal', 'deal_tunggu_approve') AND " . self::TAWARAN . ") AS deal,
                       SUM(" . self::TAWARAN . " AND EXISTS (SELECT 1 FROM sample_approvals a WHERE a.request_id = r.id)) AS approve
                FROM sample_requests r
                WHERE r.created_at >= ? AND r.created_at < ?{$toko}", [...$rentang, ...$p]);

            $chat = $this->db()->selectOne("
                SELECT COUNT(*) AS n FROM messages m JOIN sample_requests r ON r.id = m.request_id
                WHERE m.direction = 'out' AND m.status = 'sent' AND m.sent_at >= ? AND m.sent_at < ?{$toko}",
                [...$rentang, ...$p]);

            $kartu = $this->db()->select("
                SELECT TIMESTAMPDIFF(MINUTE, a.created_at, a.decided_at) AS menit
                FROM approvals a JOIN sample_requests r ON r.id = a.request_id
                WHERE a.created_at >= ? AND a.created_at < ? AND a.type <> 'tambahan_bertarget'{$toko}", [...$rentang, ...$p]);
            // tambahan_bertarget = catatan KORA (tidak dikirim ke tim), bukan kartu keputusan
            $menit = collect($kartu)->pluck('menit')->filter(fn ($m) => $m !== null && $m >= 0)->sort()->values();

            [$tokoA, $pA] = $this->whereToko();
            $jalur = $this->db()->select("
                SELECT jalur, COUNT(*) AS n FROM sample_approvals
                WHERE approved_at >= ? AND approved_at < ?{$tokoA} GROUP BY jalur", [...$rentang, ...$pA]);

            return [
                'dicek'        => (int) $f->dicek,
                'kreator'      => (int) $f->kreator,
                'ditolak'      => (int) $f->ditolak,
                'lolos'        => (int) $f->lolos,
                'ditawari'     => (int) $f->ditawari,
                'deal'         => (int) $f->deal,
                'approve'      => (int) $f->approve,
                'chat'         => (int) $chat->n,
                'kartu'        => count($kartu),
                'kartu_putus'  => $menit->count(),
                'median_putus' => $menit->count() ? (int) $menit->median() : null,
                'jalur'        => collect($jalur)->pluck('n', 'jalur')->map(fn ($n) => (int) $n)->all(),
            ];
        });
    }

    public function kinerja(): array
    {
        return Cache::remember($this->key('kinerja', now()->format('YmdH')), 1800, function () {
            [$toko, $p] = $this->whereToko();
            $rows = $this->db()->select("
                SELECT kelompok,
                       SUM(umur_hari BETWEEN ? AND ?) AS matang,
                       SUM(umur_hari BETWEEN ? AND ? AND selesai_tiktok) AS selesai,
                       SUM(umur_hari BETWEEN ? AND ? AND janji > 0) AS ada_janji,
                       SUM(umur_hari BETWEEN ? AND ? AND lengkap) AS lengkap,
                       SUM(umur_hari BETWEEN ? AND ? AND video_laku > 0) AS laku,
                       SUM(IF(umur_hari BETWEEN ? AND ?, COALESCE(views_sejak_mulai, 0), 0)) AS views,
                       SUM(IF(umur_hari BETWEEN ? AND ?, COALESCE(gmv_sejak_mulai, 0), 0)) AS gmv,
                       SUM(IF(umur_hari BETWEEN ? AND ?, COALESCE(nilai_sampel, 0), 0)) AS nilai_sampel,
                       SUM(approved_at >= ?) AS baru
                FROM v_kinerja_sampel WHERE 1 = 1{$toko} GROUP BY kelompok", [
                self::UMUR_MIN, self::UMUR_MAKS, self::UMUR_MIN, self::UMUR_MAKS,
                self::UMUR_LENGKAP, self::UMUR_MAKS, self::UMUR_LENGKAP, self::UMUR_MAKS,
                self::UMUR_MIN, self::UMUR_MAKS, self::UMUR_MIN, self::UMUR_MAKS,
                self::UMUR_MIN, self::UMUR_MAKS, self::UMUR_MIN, self::UMUR_MAKS,
                now()->subDays(7), ...$p,
            ]);
            // kelompok dibandingkan di PHP: perbandingan string di SQL kena "Illegal mix of collations" (view)
            $per = collect($rows)->keyBy(fn ($r) => (string) $r->kelompok);

            $hasil = [];
            foreach (self::KELOMPOK as $k => $label) {
                $r = $per->get($k);
                $n = (int) ($r->matang ?? 0);
                $janji = (int) ($r->ada_janji ?? 0);
                $gmv = (float) ($r->gmv ?? 0);
                $nilai = (float) ($r->nilai_sampel ?? 0);
                $hasil[$k] = [
                    'label'      => $label,
                    'matang'     => $n,
                    'selesai'    => $n ? round(100 * (int) $r->selesai / $n) : null,
                    'lengkap'    => $janji ? round(100 * (int) $r->lengkap / $janji) : null,
                    'laku'       => $n ? round(100 * (int) $r->laku / $n) : null,
                    'views'      => (int) ($r->views ?? 0),
                    'gmv'        => $gmv,
                    'per_sampel' => $n ? $gmv / $n : null,
                    'rasio'      => $nilai ? $gmv / $nilai : null,
                    'baru'       => (int) ($r->baru ?? 0),
                ];
            }
            return $hasil;
        });
    }

    /** Pengajuan yang masih bisa ditindaklanjuti (belum lewat batas TikTok), batas terdekat dulu. */
    public function antrian(?string $state, int $page, int $perPage = 10): array
    {
        $state = array_key_exists((string) $state, self::ANTRIAN) ? $state : null;

        return Cache::remember($this->key('antrian', $state ?? 'semua', $page), 300, function () use ($state, $page, $perPage) {
            [$toko, $p] = $this->whereToko();
            $states = array_keys(self::ANTRIAN);
            $isi = implode(',', array_fill(0, count($states), '?'));

            $jumlah = collect($this->db()->select("
                SELECT state, COUNT(*) AS n FROM sample_requests
                WHERE state IN ({$isi}) AND request_deadline >= NOW(){$toko} GROUP BY state", [...$states, ...$p]))
                ->pluck('n', 'state')->map(fn ($n) => (int) $n)->all();

            $filter = $state ? [$state] : $states;
            $isiF = implode(',', array_fill(0, count($filter), '?'));
            $baris = $this->db()->select("
                SELECT id, store_slug, username, state, screening_result, products, request_deadline, created_at
                FROM sample_requests
                WHERE state IN ({$isiF}) AND request_deadline >= NOW(){$toko}
                ORDER BY request_deadline ASC LIMIT ? OFFSET ?", [...$filter, ...$p, $perPage, ($page - 1) * $perPage]);

            $baris = array_map(function ($r) {
                $produk = json_decode((string) $r->products, true) ?: [];
                $r->produk = collect($produk)->pluck('product_title')->filter()->unique()->values()->all();
                unset($r->products);
                return $r;
            }, $baris);

            return [
                'jumlah' => $jumlah,
                'total'  => $state ? ($jumlah[$state] ?? 0) : array_sum($jumlah),
                'baris'  => $baris,
            ];
        });
    }

    public function penjualan(): array
    {
        return Cache::remember($this->key('penjualan', now()->format('Ymd')), 1800, function () {
            [$toko, $p] = $this->whereToko();
            [$tokoG, $pG] = $this->whereToko('g.store_slug');
            $mulai4 = now()->subDays(28)->startOfDay();

            $mingguan = $this->db()->select("
                SELECT minggu_awal, SUM(gmv) AS gmv, SUM(orders) AS orders
                FROM affiliate_video_weekly WHERE minggu_awal >= ?{$toko}
                GROUP BY minggu_awal ORDER BY minggu_awal", [now()->subDays(98)->startOfDay(), ...$p]);

            $kreator = $this->db()->select("
                SELECT g.username, g.store_slug, SUM(g.gmv_porsi) AS gmv, SUM(g.orders) AS orders,
                       COUNT(DISTINCT g.video_id) AS video,
                       MAX(EXISTS (SELECT 1 FROM sample_approvals a WHERE a.store_slug = g.store_slug
                                   AND a.product_id = g.product_id AND a.tiktok_creator_id = g.creator_oec_id)) AS sampel
                FROM v_video_produk_gabung g WHERE g.tgl_awal >= ?{$tokoG}
                GROUP BY g.username, g.store_slug ORDER BY gmv DESC LIMIT 10", [$mulai4, ...$pG]);

            $produk = $this->db()->select("
                SELECT g.product_id, g.store_slug, SUM(g.gmv_porsi) AS gmv, SUM(g.orders) AS orders,
                       COUNT(DISTINCT g.username) AS kreator,
                       (SELECT MAX(a.product_title) FROM sample_approvals a WHERE a.product_id = g.product_id) AS judul
                FROM v_video_produk_gabung g WHERE g.tgl_awal >= ? AND g.product_id IS NOT NULL{$tokoG}
                GROUP BY g.product_id, g.store_slug ORDER BY gmv DESC LIMIT 10", [$mulai4, ...$pG]);

            return compact('mingguan', 'kreator', 'produk');
        });
    }
}
