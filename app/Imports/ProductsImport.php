<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        return new Product([
            'sku_utama' => $row['sku_utama'],
            'sku'       => $row['sku'],
            'harga'     => $row['harga'],
        ]);
    }

    public function rules(): array
    {
        return [
            'sku_utama' => ['required'],
            'sku'       => ['required', 'unique:products,sku'],
            'harga'     => ['required', 'numeric'],
        ];
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