<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SendSample;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function scopeSearch($query, string $q)
    {
        $q = trim($q);

        return $query->when($q !== '', function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%");
        });
    }

    public function sendSamples()
    {
        return $this->hasMany(SendSample::class);
    }
}