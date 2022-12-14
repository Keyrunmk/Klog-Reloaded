<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        "slug",
        "title",
        "body",
        "user_id",
        "category_id",
        "location_id",
    ];

    // public function scopeFilter($query, array $filters): void
    // {
    //     $query->when(
    //         $filters["search"] ?? false,
    //         fn ($query, $search) =>
    //         $query->where(
    //             fn ($query) =>
    //             $query->where("title", "like", "%" . $search . "%")
    //                 ->orWhere("body", "like", "%" . $search . "%")
    //         )
    //     );
    // }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, "imageable");
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, "taggable");
    }

    public function postLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function location(): MorphMany
    {
        return $this->morphMany(Location::class, "locationable");
    }

    public function postReports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }
}
