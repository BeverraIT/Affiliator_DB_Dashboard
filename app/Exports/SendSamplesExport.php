<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SendSamplesExport extends DefaultValueBinder implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithColumnFormatting,
    WithColumnWidths,
    WithCustomValueBinder
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'deal_date',
            'pic',
            'approve',
            'store',
            'creator',
            'perjanjian_livestream',
            'perjanjian_video',
            'sku',
            'qty',
            'harga',
            'total',
            'id_pesanan',
            'tanggal_kirim',
            'estimasi_sampai',
            'tanggal_aktual',
            'video_dibuat',
            'live_dibuat',
        ];
    }

    public function map($row): array
    {
        $harga = (int) ($row->product->harga ?? 0);
        $qty   = (int) ($row->qty ?? 0);
        $total = $harga * $qty;

        return [
            $this->formatDate($row->deal_date),
            $row->user->name ?? '-',
            $row->approver->name ?? '-',
            $row->store->name ?? '-',
            $row->creator->username ?? $row->creator->name ?? '-',
            (int) $row->perjanjian_livestream,
            (int) $row->perjanjian_video,
            $row->product->sku ?? '-',
            $qty,
            $harga,
            $total,
            $row->id_pesanan ?? '-',
            $this->formatDate($row->tanggal_kirim),
            $this->formatDate($row->estimasi_sampai),
            $this->formatDate($row->tanggal_aktual),
            (int) $row->video_dibuat,
            (int) $row->live_dibuat,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'L' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'L' => 30,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'L') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function formatDate($date)
    {
        return $date ? $date->format('Y-m-d') : '-';
    }
}