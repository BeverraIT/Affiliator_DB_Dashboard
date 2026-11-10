<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Data halaman KORA › Kreator Fit (keputusan 10 Nov 2026). Hanya SELECT ke DB creator_agent (koneksi 'kora').
 *
 * "Fit" = kreator pernah menjual produk kita (toko mana pun) menurut data penjualan video mingguan,
 * dihitung SEBELUM sampel dimulai supaya tidak bocor dari hasil sampel itu sendiri.
 * Definisi sama dengan creator-agent: src/creator_agent/fit.py dan uji mundur di vault
 * ("Kajian Ide - Product-Creator Fit ...", bagian 5A).
 */
class KoraFit
{
    public const UMUR_MIN = 21;
    public const UMUR_MAKS = 120;
    public const BATAS_GMV = 10000000;

    public function __construct(private ?string $toko = null)
    {
        $this->toko = array_key_exists((string) $toko, KoraStats::TOKO) ? $toko : null;
    }

    private function db()
    {
        return DB::connection('kora');
    }

    private function key(string $nama, ...$bagian): string
    {
        return 'kora-fit:' . $nama . ':' . ($this->toko ?? 'semua') . ':' . implode(':', $bagian);
    }

    private function whereToko(string $kolom): array
    {
        return $this->toko ? [" AND {$kolom} = ?", [$this->toko]] : ['', []];
    }

    /**
     * Uji mundur: sampel matang (21–120 hari) dibagi fit / tidak fit, dan dibandingkan dengan batas GMV 30 hari
     * screening. -> ['fit' => baris, 'tidak' => baris, 'silang' => [[gmv_lolos, fit] => baris]].
     */
    public function ujiMundur(): array
    {
        return Cache::remember($this->key('uji', now()->format('YmdH')), 3600, function () {
            [$toko, $p] = $this->whereToko('k.store_slug');
            $rows = $this->db()->select("
                SELECT fit, gmv_lolos, COUNT(*) AS n, SUM(video_laku > 0) AS laku,
                       COALESCE(SUM(gmv_sejak_mulai), 0) AS gmv, COALESCE(SUM(nilai_sampel), 0) AS nilai
                FROM (
                    SELECT k.video_laku, k.gmv_sejak_mulai, k.nilai_sampel,
                           CASE WHEN JSON_VALUE(r.screening_checks, '$.gmv.nilai') IS NULL THEN NULL
                                WHEN CAST(JSON_VALUE(r.screening_checks, '$.gmv.nilai') AS DECIMAL(16,2)) >= ? THEN 1
                                ELSE 0 END AS gmv_lolos,
                           EXISTS (SELECT 1 FROM v_video_produk_mingguan w
                                   WHERE w.creator_oec_id = k.tiktok_creator_id
                                     AND w.minggu_akhir < k.tanggal_mulai AND w.gmv_porsi > 0) AS fit
                    FROM v_kinerja_sampel k
                    LEFT JOIN sample_requests r ON r.id = k.request_id
                    WHERE k.umur_hari BETWEEN ? AND ? {$toko}
                ) x
                GROUP BY fit, gmv_lolos", array_merge([self::BATAS_GMV, self::UMUR_MIN, self::UMUR_MAKS], $p));

            $kosong = ['n' => 0, 'laku' => 0, 'gmv' => 0.0, 'nilai' => 0.0];
            $tambah = function (array $a, object $r): array {
                return ['n' => $a['n'] + (int) $r->n, 'laku' => $a['laku'] + (int) $r->laku,
                        'gmv' => $a['gmv'] + (float) $r->gmv, 'nilai' => $a['nilai'] + (float) $r->nilai];
            };
            $hasil = ['fit' => $kosong, 'tidak' => $kosong, 'semua' => $kosong, 'silang' => []];
            foreach ($rows as $r) {
                $hasil[$r->fit ? 'fit' : 'tidak'] = $tambah($hasil[$r->fit ? 'fit' : 'tidak'], $r);
                $hasil['semua'] = $tambah($hasil['semua'], $r);
                if ($r->gmv_lolos !== null) {
                    $k = ((int) $r->gmv_lolos) . ':' . ((int) $r->fit);
                    $hasil['silang'][$k] = $tambah($hasil['silang'][$k] ?? $kosong, $r);
                }
            }
            foreach (['fit', 'tidak', 'semua'] as $k) {
                $hasil[$k] = $this->rasio($hasil[$k]);
            }
            foreach ($hasil['silang'] as $k => $v) {
                $hasil['silang'][$k] = $this->rasio($v);
            }
            return $hasil;
        });
    }

    private function rasio(array $a): array
    {
        $a['pct_laku']   = $a['n'] ? round($a['laku'] * 100 / $a['n']) : null;
        $a['per_sampel'] = $a['n'] ? $a['gmv'] / $a['n'] : null;
        $a['kali']       = $a['nilai'] > 0 ? round($a['gmv'] / $a['nilai'], 1) : null;
        return $a;
    }

    /**
     * Kreator terbukti: sudah menjual produk kita tapi belum pernah tercatat menerima sampel (sinkron TikTok sejak Okt).
     * -> ['total' => int, 'baris' => [...]], urut GMV terbesar.
     */
    public function terbukti(int $page, int $perPage = 20): array
    {
        return Cache::remember($this->key('terbukti', now()->format('YmdH'), $page), 3600, function () use ($page, $perPage) {
            [$toko, $p] = $this->whereToko('store_slug');
            $inti = "FROM v_kreator_produk WHERE 1=1 {$toko}
                     GROUP BY store_slug, creator_oec_id HAVING MAX(dapat_sampel) = 0 AND SUM(gmv_porsi) > 0";
            $total = (int) $this->db()->selectOne("SELECT COUNT(*) AS n FROM (SELECT 1 {$inti}) x", $p)->n;
            $baris = $this->db()->select("
                SELECT store_slug, creator_oec_id, MAX(username) AS username, SUM(gmv_porsi) AS gmv, SUM(orders) AS orders,
                       SUM(jumlah_video) AS video, COUNT(DISTINCT product_id) AS produk, MAX(tanggal_terakhir) AS terakhir
                {$inti}
                ORDER BY gmv DESC LIMIT ? OFFSET ?", array_merge($p, [$perPage, ($page - 1) * $perPage]));
            return ['total' => $total, 'baris' => $baris];
        });
    }

    /** Kartu "Kreator fit" yang dikirim KORA ke tim (aturan 10 Nov) beserta keputusan tim. */
    public function kartuFit(int $batas = 30): array
    {
        return Cache::remember($this->key('kartu', now()->format('YmdHi')), 300, function () use ($batas) {
            [$toko, $p] = $this->whereToko('r.store_slug');
            return $this->db()->select("
                SELECT a.id, a.decision, a.created_at, r.store_slug, r.username, r.state
                FROM approvals a JOIN sample_requests r ON r.id = a.request_id
                WHERE a.type = 'fit' {$toko}
                ORDER BY a.created_at DESC LIMIT ?", array_merge($p, [$batas]));
        });
    }
}
