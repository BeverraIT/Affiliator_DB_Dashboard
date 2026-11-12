<?php

namespace App\Imports;

use App\Models\Creator;
use App\Models\Product;
use App\Models\SendSample;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SendSampleImport implements SkipsEmptyRows, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow
{
    /** Lookup maps di-preload sekali (menghindari 5 query per baris). */
    protected Collection $stores;

    protected Collection $creators;

    protected Collection $users;

    protected Collection $products;

    /** Jumlah baris dilewati karena referensi wajib tidak ditemukan. */
    public int $skipped = 0;

    /** Jumlah baris dilewati karena sudah ada (di database atau di file yang sama). */
    public int $duplicates = 0;

    /** Kunci "kreator|produk|tanggal deal" dan ID pesanan yang sudah ada. */
    protected array $existingKeys = [];

    protected array $existingOrders = [];

    public function __construct()
    {
        $this->stores = Store::get(['id', 'name'])->keyBy(fn ($s) => strtoupper(trim($s->name)));
        $this->creators = Creator::get(['id', 'username'])->keyBy(fn ($c) => strtolower(trim($c->username)));
        $this->users = User::get(['id', 'name'])->keyBy(fn ($u) => ucwords(strtolower(trim($u->name))));
        $this->products = Product::get(['id', 'sku'])->keyBy(fn ($p) => trim($p->sku));

        SendSample::query()->toBase()
            ->select(['id', 'creator_id', 'product_id', 'deal_date', 'id_pesanan'])
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $r) {
                    $this->existingKeys[$this->key($r->creator_id, $r->product_id, $r->deal_date)] = true;
                    if (($id = trim((string) $r->id_pesanan)) !== '') {
                        $this->existingOrders[$id] = true;
                    }
                }
            });
    }

    private function key($creatorId, $productId, $dealDate): string
    {
        return $creatorId.'|'.$productId.'|'.($dealDate ? Carbon::parse($dealDate)->format('Y-m-d') : '');
    }

    public function model(array $row)
    {
        $store = $this->stores->get(strtoupper(trim($row['toko'] ?? '')));
        $creator = $this->creators->get(strtolower(trim($row['kreator'] ?? '')));
        $pic = $this->users->get(ucwords(strtolower(trim($row['pic'] ?? ''))));
        $product = $this->products->get(trim($row['sku'] ?? ''));

        // Kolom FK wajib (NOT NULL). Lewati baris yang tak cocok agar import
        // tidak gagal total (sebelumnya menyebabkan error insert FK null).
        if (! $store || ! $creator || ! $product || ! $pic) {
            $this->skipped++;

            return null;
        }

        $approver = $this->users->get(ucwords(strtolower(trim($row['approve'] ?? ''))));

        $tanggalKirim = $this->tanggal($row['tanggal_kirim'] ?? null);
        $estimasiSampai = $tanggalKirim ? Carbon::instance($tanggalKirim)->copy()->addDays(7) : null;
        $dealDate = $this->tanggal($row['deal_date'] ?? null);
        $tanggalAktual = $this->tanggal($row['tanggal_aktual'] ?? null);

        // Cegah duplikat: kreator + produk + tanggal deal yang sama (aturan yang sama dengan API KORA),
        // atau ID pesanan yang sudah tercatat. Berlaku terhadap database dan baris lain di file ini.
        $idPesanan = trim((string) ($row['id_pesanan'] ?? ''));
        $kunci = $this->key($creator->id, $product->id, $dealDate);
        if (isset($this->existingKeys[$kunci]) || ($idPesanan !== '' && isset($this->existingOrders[$idPesanan]))) {
            $this->duplicates++;

            return null;
        }
        $this->existingKeys[$kunci] = true;
        if ($idPesanan !== '') {
            $this->existingOrders[$idPesanan] = true;
        }

        return new SendSample([
            'deal_date' => $dealDate,
            'pic' => $pic->id,
            'approve' => $approver?->id,
            'store_id' => $store->id,
            'creator_id' => $creator->id,
            'product_id' => $product->id,
            'tanggal_kirim' => $tanggalKirim,
            'estimasi_sampai' => $estimasiSampai,
            'tanggal_aktual' => $tanggalAktual,
            'qty' => (int) ($row['qty'] ?? 0),
            'id_pesanan' => $idPesanan !== '' ? $idPesanan : null,
            'video_dibuat' => $row['video_dibuat'] ?? null,
            'live_dibuat' => $row['live_dibuat'] ?? null,
            'perjanjian_livestream' => $row['perjanjian_livestream'] ?? null,
            'perjanjian_video' => $row['perjanjian_video'] ?? null,
        ]);
    }

    /** Serial Excel atau teks tanggal ("2026-11-12") -> DateTime; kosong/tak terbaca -> null. */
    private function tanggal($nilai): ?\DateTimeInterface
    {
        if ($nilai === null || $nilai === '') {
            return null;
        }
        try {
            return is_numeric($nilai) ? Date::excelToDateTimeObject((float) $nilai) : Carbon::parse((string) $nilai);
        } catch (\Throwable $e) {
            return null;
        }
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
