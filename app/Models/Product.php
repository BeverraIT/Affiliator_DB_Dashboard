<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'products';
    public $timestamps = false;
    
    protected $fillable = [
        'sku_utama',
        'sku',
        'harga',
    ];

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