<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SkuExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $products;
    protected $rank = 0;

    // Menerima data dari Controller
    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products;
    }

    // Menentukan Header Kolom Excel
    public function headings(): array
    {
        return [
            'Rank',
            'Nama SKU',
            'Harga Satuan (Rp)',
            'Total Qty Dikirim',
            'Total Pengeluaran (Rp)'
        ];
    }

    // Memetakan setiap baris data
    public function map($product): array
    {
        $this->rank++;

        return [
            $this->rank,
            $product->sku_utama,
            $product->harga, // Menambahkan data harga satuan ke baris Excel
            $product->total_qty,
            $product->total_pengeluaran
        ];
    }

    // Memberikan style tebal pada baris pertama (Header)
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}