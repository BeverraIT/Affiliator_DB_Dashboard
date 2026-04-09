<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function creators(): HasMany
    {
        return $this->hasMany(Creator::class);
    }

    // public function sendSamples(): HasMany
    // {
    //     return $this->hasMany(SendSample::class);
    // }

    public function scopeSearch($query, $q)
    {
        $q = trim($q);
        $like = '%' . $q . '%';

        return $query->where(function ($w) use ($like) {
            $w->where('name', 'like', $like)
              ->orWhere('email', 'like', $like);
        });
    }
}