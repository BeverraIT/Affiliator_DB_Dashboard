<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Pastikan Log di-import
use App\Models\Creator;
use App\Models\Store;
use App\Models\Product;
use App\Models\User;

class DatabaseResponseTimeTest extends TestCase
{
    public function test_measure_database_and_hydration_time()
    {
        // 1. Aktifkan Query Log
        DB::enableQueryLog();

        // 2. Mulai stopwatch untuk PHP
        $startTime = microtime(true);

        // --- MULAI KODE YANG DIUJI ---
        $creators   = Creator::where('is_blocked', 0)->orderBy('username')->get();
        $stores     = Store::orderBy('name')->get();
        $products   = Product::orderBy('sku')->get();
        $users      = User::orderBy('name')->get();
        // --- SELESAI KODE YANG DIUJI ---

        // versi optimasi
        // $creators   = Creator::where('is_blocked', 0)->orderBy('username')->pluck('username', 'id');
        // $stores     = Store::orderBy('name')->pluck('name', 'id');
        // $products   = Product::orderBy('sku_utama')->pluck('sku_utama', 'id');
        // $users      = User::orderBy('name')->pluck('name', 'id');

        // 3. Hentikan stopwatch PHP
        $endTime = microtime(true);
        $totalPhpTime = round(($endTime - $startTime) * 1000, 2); 

        // 4. Hitung total waktu murni database
        $queries = DB::getQueryLog();
        $totalDbTime = 0;
        
        // Siapkan pesan yang akan dimasukkan ke Log
        $logMessage = "\n=== HASIL PROFILING DATABASE ===\n";
        
        foreach ($queries as $index => $query) {
            $totalDbTime += $query['time'];
            $logMessage .= "Query " . ($index + 1) . " time: " . $query['time'] . " ms \n";
        }

        $logMessage .= "---------------------------------\n";
        $logMessage .= "Total Waktu Murni DB   : {$totalDbTime} ms\n";
        $logMessage .= "Total Waktu DB + PHP   : {$totalPhpTime} ms\n";
        $logMessage .= "Selisih (Model Hydrate): " . ($totalPhpTime - $totalDbTime) . " ms\n";
        $logMessage .= "Total Memori Digunakan : " . round(memory_get_usage() / 1024 / 1024, 2) . " MB\n";
        $logMessage .= "=================================\n";

        // 5. Simpan ke file Log Laravel
        Log::info($logMessage);

        // Assert sederhana
        $this->assertTrue(true);
    }
}