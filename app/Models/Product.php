<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    public $timestamps = false;
    
    protected $fillable = [
        'sku_utama',
        'sku',
        'harga',
    ];

    // public function send_samples(): HasMany
    // {
    //     return $this->hasMany(SendSample::class);
    // }

    public function scopeSearch($query, $q)
    {
        $q = trim($q);
        if ($q === '') {
            return $query;
        }
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';

        return $query->where(function ($w) use ($like) {
            $w->where('sku_utama', 'like', $like)
              ->orWhere('sku', 'like', $like);
        });
    }
}