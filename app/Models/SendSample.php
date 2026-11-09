<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SendSample extends Model
{
    protected $table = 'send_sample';

    public $timestamps = false;

    protected $fillable = [
        'creator_id',
        'store_id',
        'product_id',
        'seller_product_id',
        'pic',
        'approve',
        'deal_date',
        'tanggal_kirim',
        'estimasi_sampai',
        'tanggal_aktual',
        'qty',
        'id_pesanan',
        'perjanjian_livestream',
        'perjanjian_video',
        'video_dibuat',
        'status_follow_up_konten_mingguan',
        'status_chat',
        'tanggal_dichat',
    ];

    protected $casts = [
        'deal_date' => 'date',
        'tanggal_kirim' => 'date',
        'estimasi_sampai' => 'date',
        'tanggal_aktual' => 'date',
        'status_chat' => 'integer',
        'tanggal_dichat' => 'datetime',
    ];

    public function scopeDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->where('deal_date', '>=', $startDate)
            ->where('deal_date', '<=', $endDate);
    }

    public function scopeByPic(Builder $query, $userId)
    {
        return $query->when($userId, fn ($q) => $q->where('pic', $userId));
    }

    /**
     * Baris yang boleh dilihat user: super admin semuanya; selain itu baris PIC-nya sendiri
     * ditambah baris buatan KORA (PIC = akun KORA), supaya hasil kerja KORA terlihat oleh seluruh tim.
     */
    public function scopeVisibleTo(Builder $query, $user)
    {
        if (! $user || $user->role === 'super admin') {
            return $query;
        }

        $kora = config('kora.user_id');

        return $query->where(fn ($q) => $q->where('pic', $user->id)
            ->when($kora, fn ($w) => $w->orWhere('pic', $kora)));
    }

    /** Baris ini dibuat KORA (PIC = akun KORA). */
    public function isKora(): bool
    {
        $kora = config('kora.user_id');

        return $kora && (int) $this->pic === (int) $kora;
    }

    public function scopePendingContent(Builder $query)
    {
        return $query->whereNotNull('tanggal_dichat')
            ->where('status_follow_up_konten_mingguan', 0)
            ->where('tanggal_dichat', '<=', now()->subDays(3)->toDateString())
            ->where(fn ($q) => $q
                ->whereColumn('video_dibuat', '<', 'perjanjian_video')
            );
    }

    public function scopeNeedFollowUp(Builder $query)
    {
        return $query->whereNull('tanggal_aktual')
            ->whereNotNull('estimasi_sampai')
            ->where('estimasi_sampai', '<=', Carbon::today()->addDays(3)->toDateString());
    }

    public function scopeBySkuUtama(Builder $query, $sku_utama)
    {
        return $query->when($sku_utama, function ($q) use ($sku_utama) {
            $q->whereHas('product', fn ($p) => $p->where('sku_utama', $sku_utama));
        });
    }

    public function scopeSearch(Builder $query, ?string $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $like = "%{$search}%";
            $q->where(fn ($sub) => $sub
                ->where('id_pesanan', 'like', $like)
                ->orWhereHas('creator', fn ($c) => $c
                    ->where('username', 'like', $like)
                    ->orWhere('namawa', 'like', $like))
                ->orWhereHas('store', fn ($s) => $s->where('name', 'like', $like))
                ->orWhereHas('product', fn ($p) => $p->where('sku', 'like', $like))
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like))
            );
        });
    }

    public static function calculateTotalExpense()
    {
        return self::join('products', 'send_sample.product_id', '=', 'products.id')
            ->sum(DB::raw('send_sample.qty * products.harga'));
    }

    public function scopeUnfulfilledAgreement(Builder $query)
    {
        return $query->whereColumn('video_dibuat', '<', 'perjanjian_video');
    }

    public function scopeNeedContentFollowUp(Builder $query)
    {
        return $query->pendingContent()
            ->where('status_chat', 1);
    }

    public function scopeNeedChatFollowUp(Builder $query): Builder
    {
        return $query->whereNotNull('tanggal_aktual')
            ->where('status_chat', 0);
    }

    public function scopeFollowUpChat(Builder $query)
    {
        return $query->whereNotNull('tanggal_aktual')
            ->where('status_chat', 0)
            ->orderByRaw('tanggal_aktual DESC')
            ->orderByRaw('deal_date IS NULL, deal_date DESC')
            ->orderByDesc('id');
    }

    public function scopeFilterDate($query, $start, $end, $column = 'deal_date')
    {
        if ($start && $end) {
            return $query->where($column, '>=', $start)
                ->where($column, '<=', $end);
        }

        return $query;
    }

    public function getTotalNilaiAttribute()
    {
        if ($this->product) {
            return $this->qty * $this->product->harga;
        }

        return 0;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pic');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approve');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function creator()
    {
        return $this->belongsTo(Creator::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
