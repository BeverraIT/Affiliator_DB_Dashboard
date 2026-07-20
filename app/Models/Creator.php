<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Creator extends Model
{
    protected $fillable = [
        'user_id',
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

    /**
     * Normalisasi username satu pintu (form, import, cek AJAX) agar
     * "Foo", "@foo", dan " foo " selalu dianggap sama.
     */
    public static function normalizeUsername(string $username): string
    {
        return strtolower(ltrim(trim($username), '@'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';

        return $query->where(function ($w) use ($like) {
            $w->where('username', 'like', $like)
                ->orWhere('namawa', 'like', $like)
                ->orWhere('nomerwa', 'like', $like);
        });
    }

    public function scopeRecent($query, $days = 7)
    {
        // created_at bertipe datetime: pakai start-of-day agar index terpakai
        // tanpa membungkus kolom dalam fungsi DATE().
        return $query->where('created_at', '>=', now()->subDays($days)->startOfDay());
    }

    public function scopeFilterDate($query, $start, $end, $column = 'created_at')
    {
        if ($start && $end) {
            // Half-open range [start 00:00, end+1 00:00) — menjaga semantik
            // "sampai akhir hari end" untuk kolom datetime sekaligus index-friendly.
            return $query->where($column, '>=', Carbon::parse($start)->startOfDay())
                ->where($column, '<', Carbon::parse($end)->addDay()->startOfDay());
        }

        return $query;
    }
}
