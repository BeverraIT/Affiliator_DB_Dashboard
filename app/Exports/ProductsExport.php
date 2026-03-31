<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    // 1. Mengambil data menggunakan Query Builder
    public function query()
    {
        return Product::query();
    }

    // 2. Menentukan ukuran chunk
    public function chunkSize(): int
    {
        return 1000;
    }

    // 3. Menentukan Header di Kolom Excel
    public function headings(): array
    {
        return [
            'sku_utama',
            'sku',
            'harga',
        ];
    }

    // 4. Mapping data
    public function map($product): array
    {
        return [
            $product->sku_utama,
            $product->sku,
            $product->harga,
        ];
    }
}