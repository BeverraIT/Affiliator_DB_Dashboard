<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Creator extends Model
{
    protected $fillable = [
        'username',
        'channel',
        'gmv',
        'itemsterjual',
        'genderpengikut',
        'namawa',
        'nomerwa',
        'top_creator',
        'is_blocked',
    ];

    public function contentStyles(): BelongsToMany
    {
        return $this->belongsToMany(
            ContentStyle::class,
            'content_style_creator',
            'creator_id',
            'content_style_id',
        );
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_creator',
            'creator_id',
            'category_id',
        );
    }

    public function ages(): BelongsToMany
    {
        return $this->belongsToMany(
            Age::class,
            'age_creator',
            'creator_id',
            'age_id',
        );
    }

    public function sendSamples()
    {
        return $this->hasMany(SendSample::class);
    }

    public function scopeSearch($query, $q)
    {
        $q = trim($q);
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';

        return $query->where(function ($w) use ($like) {
            $w->where('username', 'like', $like)
              ->orWhere('namawa', 'like', $like)
              ->orWhere('nomerwa', 'like', $like);
        });
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->whereDate('created_at', '>=', now()->subDays($days));
    }

    public function scopeFilterDate($query, $start, $end, $column = 'created_at')
    {
        if ($start && $end) {
            return $query->whereDate($column, '>=', $start)
                         ->whereDate($column, '<=', $end);
        }

        return $query;
    }
}