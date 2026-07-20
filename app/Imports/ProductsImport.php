<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements SkipsEmptyRows, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow
{
    /** Set SKU yang sudah ada (di-preload sekali, dedupe in-memory — bukan query per baris). */
    protected Collection $existingSkus;

    public int $imported = 0;

    /** Baris dilewati karena SKU sudah ada (di DB atau duplikat di dalam file). */
    public int $skippedDuplicate = 0;

    /** Baris dilewati karena data tidak valid (SKU kosong/terlalu panjang, harga bukan angka/negatif). */
    public int $skippedInvalid = 0;

    private bool $headerChecked = false;

    public function __construct()
    {
        $this->existingSkus = Product::pluck('sku')
            ->map(fn ($sku) => mb_strtoupper(trim((string) $sku)))
            ->flip();
    }

    public function model(array $row)
    {
        $this->assertTemplateHeaders($row);

        $skuUtama = trim((string) ($row['sku_utama'] ?? ''));
        $sku = trim((string) ($row['sku'] ?? ''));
        $harga = $this->parseHarga($row['harga'] ?? null);

        if ($skuUtama === '' || $sku === ''
            || mb_strlen($skuUtama) > 150 || mb_strlen($sku) > 150
            || $harga === null || $harga < 0) {
            $this->skippedInvalid++;

            return null;
        }

        // Dedupe terhadap DB dan antar-baris dalam file yang sama.
        $skuKey = mb_strtoupper($sku);
        if ($this->existingSkus->has($skuKey)) {
            $this->skippedDuplicate++;

            return null;
        }
        $this->existingSkus->put($skuKey, true);
        $this->imported++;

        return new Product([
            'sku_utama' => $skuUtama,
            'sku' => $sku,
            'harga' => $harga,
        ]);
    }

    /**
     * Pastikan header file sesuai template; tanpa ini kolom yang salah
     * hanya memunculkan error "undefined array key" yang membingungkan.
     */
    private function assertTemplateHeaders(array $row): void
    {
        if ($this->headerChecked) {
            return;
        }
        $this->headerChecked = true;

        foreach (['sku_utama', 'sku', 'harga'] as $key) {
            if (! array_key_exists($key, $row)) {
                throw new \InvalidArgumentException(
                    'Template tidak sesuai: kolom sku_utama, sku, dan harga wajib ada. Silakan unduh template terbaru.'
                );
            }
        }
    }

    /**
     * Terima angka Excel apa adanya; untuk teks, bersihkan format rupiah
     * ("Rp 10.000", "10.000,50") sebelum dikonversi. Null jika tetap bukan angka.
     */
    private function parseHarga($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }

        $str = str_replace(['rp', ' '], '', strtolower(trim((string) $value)));

        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $str)) {
            // Format Indonesia: titik ribuan, koma desimal.
            $str = str_replace('.', '', $str);
        }
        $str = str_replace(',', '.', $str);

        return is_numeric($str) ? (float) $str : null;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
