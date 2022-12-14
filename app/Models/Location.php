<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ["country_name"];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function locationable(): MorphTo
    {
        return $this->morphTo();
    }
}
