<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Age extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    public function creators(): BelongsToMany
    {
        return $this->belongsToMany(
            Creator::class, 
            'age_creator',
            'age_id',
            'creator_id',
        );
    }
}
