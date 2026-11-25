<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KoraBoost;
use App\Services\KoraStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * KORA › Boost (25 Nov 2026): kreator yang diundang per kampanye boost, produk, status, tanggal, video, hasil.
 * Hanya membaca DB creator_agent lewat KoraBoost; tidak ada aksi tulis.
 */
class KoraBoostController extends Controller
{
    public function index(Request $request)
    {
        $boost = app(KoraBoost::class);
        $page  = max(1, (int) $request->query('page', 1));
        $filter = [
            'toko'   => array_key_exists((string) $request->query('toko'), KoraStats::TOKO) ? $request->query('toko') : null,
            'sku'    => $request->query('sku') ?: null,
            'status' => $request->query('status') ?: null,
            'cari'   => $request->query('cari') ?: null,
        ];

        $kampanye = [];
        $k = $ringkas = $produk = $daftar = null;
        $belumPeta = [];
        $galat = null;
        try {
            $kampanye = $boost->kampanye();
            $kode = $request->query('kampanye');
            $k = collect($kampanye)->firstWhere('kode', $kode) ?? ($kampanye[0] ?? null);
            if ($k) {
                $ringkas   = $boost->ringkasan($k);
                $produk    = $boost->perProduk($k);
                $belumPeta = $boost->belumTerpetakan($k);
                $daftar    = $boost->daftar($k, $filter, $page);
            }
        } catch (\Throwable $e) {
            Log::warning('Halaman KORA Boost: gagal membaca DB creator_agent', ['error' => $e->getMessage()]);
            $galat = 'Data Boost belum bisa dibaca. Hubungi admin untuk mengecek koneksi database KORA '
                   . '(tabel boost dibuat lewat migrations/020_boost.sql di creator-agent).';
        }

        return view('admin.kora.boost', [
            'kampanye'   => $kampanye,
            'k'          => $k,
            'ringkas'    => $ringkas,
            'produk'     => $produk,
            'belumPeta'  => $belumPeta,
            'daftar'     => $daftar,
            'filter'     => $filter,
            'page'       => $page,
            'daftarToko' => KoraStats::TOKO,
            'galat'      => $galat,
        ]);
    }
}
