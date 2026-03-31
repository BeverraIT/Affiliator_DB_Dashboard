<?php

namespace App\Imports;

use App\Models\SendSample;
use App\Models\User;
use App\Models\Store;
use App\Models\Creator;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class SendSampleImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $namaToko = strtoupper(trim($row['toko'] ?? ''));
        $store    = Store::where('name', $namaToko)->first();

        $usernameKreator = strtolower(trim($row['kreator'] ?? ''));
        $creator  = Creator::where('username', $usernameKreator)->first();

        $namaPic  = ucwords(strtolower(trim($row['pic'] ?? '')));
        $pic      = User::where('name', $namaPic)->first();

        $namaAppr = ucwords(strtolower(trim($row['approve'] ?? '')));
        $approver = User::where('name', $namaAppr)->first();

        $sku      = trim($row['sku'] ?? '');
        $product  = Product::where('sku', $sku)->first();

        $tanggalKirim = $row['tanggal_kirim'] ? Date::excelToDateTimeObject($row['tanggal_kirim']) : null;
        $estimasiSampai = null;
        $durasiPengiriman = 7;

        if ($tanggalKirim) {
            $estimasiSampai = Carbon::instance($tanggalKirim)->copy()->addDays($durasiPengiriman);
        }

        $dealDate      = $row['deal_date'] ? Date::excelToDateTimeObject($row['deal_date']) : null;
        $tanggalAktual = $row['tanggal_aktual'] ? Date::excelToDateTimeObject($row['tanggal_aktual']) : null;

        return new SendSample ([
            'deal_date'             => $dealDate,
            'pic'                   => $pic ? $pic->id : null,
            'approve'               => $approver ? $approver->id : null,
            'store_id'              => $store ? $store->id : null,
            'creator_id'            => $creator ? $creator->id : null,
            'product_id'            => $product ? $product->id : null,
            'tanggal_kirim'         => $tanggalKirim,
            'estimasi_sampai'       => $estimasiSampai,
            'tanggal_aktual'        => $tanggalAktual,
            'qty'                   => $row['qty'],
            'id_pesanan'            => $row['id_pesanan'],
            'video_dibuat'          => $row['video_dibuat'],
            'live_dibuat'           => $row['live_dibuat'],
            'perjanjian_livestream' => $row['perjanjian_livestream'],
            'perjanjian_video'      => $row['perjanjian_video'],
        ]);
    }
}