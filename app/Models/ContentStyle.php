<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContentStyle extends Model
{
    protected $table = 'contentstyles';
    protected $fillable = ['name'];
    public $timestamps = false;

    public function creators(): BelongsToMany
    {
        return $this->belongsToMany(
            Creator::class,
            'content_style_creator',
            'content_style_id',
            'creator_id',
        );
    }

    public function scopeSearch($query, string $q)
    {
        $q = trim($q);

        return $query->when($q !== '', function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%");
        });
    }
}