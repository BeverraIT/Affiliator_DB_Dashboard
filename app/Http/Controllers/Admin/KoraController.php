<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KoraStats;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Halaman KORA (creator agent): ringkasan, kinerja sampel, antrian, penjualan video.
 * Hanya membaca DB creator_agent lewat KoraStats; tidak ada aksi tulis.
 */
class KoraController extends Controller
{
    private const TAB = ['ringkasan', 'kinerja', 'antrian', 'penjualan'];
    private const MULAI_KORA = '2026-10-03';

    public function index(Request $request)
    {
        $tab  = in_array($request->query('tab'), self::TAB, true) ? $request->query('tab') : 'ringkasan';
        $toko = $request->query('toko');
        $stats = app()->make(KoraStats::class, ['toko' => $toko]);
        $toko = array_key_exists((string) $toko, KoraStats::TOKO) ? $toko : null;

        $today  = Carbon::today();
        $period = $request->query('period', '30d');
        if ($period === 'custom') {
            $dari   = $this->tanggal($request->query('date_from'), $today);
            $sampai = $this->tanggal($request->query('date_to'), $today);
            if ($dari->gt($sampai)) {
                [$dari, $sampai] = [$sampai, $dari];
            }
            $periodLabel = $dari->locale('id')->isoFormat('D MMM YYYY') . ' – ' . $sampai->locale('id')->isoFormat('D MMM YYYY');
        } else {
            [$dari, $sampai, $periodLabel, $period] = match ($period) {
                '7d'    => [$today->copy()->subDays(6), $today->copy(), '7 hari terakhir', '7d'],
                'semua' => [Carbon::parse(self::MULAI_KORA), $today->copy(), 'Sejak KORA mulai (3 Okt 2026)', 'semua'],
                default => [$today->copy()->subDays(29), $today->copy(), '30 hari terakhir', '30d'],
            };
        }

        $data = [];
        $galat = null;
        try {
            $data = match ($tab) {
                'kinerja'   => ['kinerja' => $stats->kinerja()],
                'antrian'   => ['antrian' => $stats->antrian($request->query('state'), max(1, (int) $request->query('page', 1)))],
                'penjualan' => ['penjualan' => $stats->penjualan()],
                default     => ['ringkasan' => $stats->ringkasan($dari, $sampai)],
            };
        } catch (\Throwable $e) {
            // DB KORA belum bisa dibaca (izin/koneksi): halaman tetap terbuka dengan pesan.
            Log::warning('Halaman KORA: gagal membaca DB creator_agent', ['error' => $e->getMessage()]);
            $galat = 'Data KORA belum bisa dibaca. Hubungi admin untuk mengecek koneksi database KORA.';
        }

        return view('admin.kora.index', array_merge($data, [
            'tab'          => $tab,
            'toko'         => $toko,
            'daftarToko'   => KoraStats::TOKO,
            'period'       => $period,
            'periodLabel'  => $periodLabel,
            'dari'         => $dari,
            'sampai'       => $sampai,
            'state'        => $request->query('state'),
            'galat'        => $galat,
        ]));
    }

    private function tanggal(?string $nilai, Carbon $cadangan): Carbon
    {
        try {
            return $nilai ? Carbon::parse($nilai)->startOfDay() : $cadangan->copy();
        } catch (\Throwable) {
            return $cadangan->copy();
        }
    }
}
