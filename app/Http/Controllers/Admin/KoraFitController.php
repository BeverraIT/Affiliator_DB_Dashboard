<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KoraFit;
use App\Services\KoraStats;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * KORA › Kreator Fit (10 Nov 2026): histori kolaborasi sebagai rujukan memilih kreator.
 * Hanya membaca DB creator_agent lewat KoraFit; tidak ada aksi tulis.
 */
class KoraFitController extends Controller
{
    public function index(Request $request)
    {
        $toko = $request->query('toko');
        $fit  = app()->make(KoraFit::class, ['toko' => $toko]);
        $toko = array_key_exists((string) $toko, KoraStats::TOKO) ? $toko : null;
        $page = max(1, (int) $request->query('page', 1));

        $uji = $terbukti = null;
        $kartu = [];
        $galat = null;
        try {
            $uji      = $fit->ujiMundur();
            $terbukti = $fit->terbukti($page);
            $kartu    = $fit->kartuFit();
        } catch (\Throwable $e) {
            Log::warning('Halaman KORA Kreator Fit: gagal membaca DB creator_agent', ['error' => $e->getMessage()]);
            $galat = 'Data KORA belum bisa dibaca. Hubungi admin untuk mengecek koneksi database KORA.';
        }

        return view('admin.kora.fit', [
            'toko'       => $toko,
            'daftarToko' => KoraStats::TOKO,
            'uji'        => $uji,
            'terbukti'   => $terbukti,
            'kartu'      => $kartu,
            'page'       => $page,
            'galat'      => $galat,
        ]);
    }
}
