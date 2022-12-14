<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "first_name",
        "last_name",
        "username",
        "email",
        "role_id",
        "password",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        "password",
        "remember_token",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "email_verified_at" => "datetime",
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     */
    public function getJWTCustomClaims(): mixed
    {
        return [];
    }

    // public static function boot(): void
    // {
    //     parent::boot();

    //     static::created(function ($user) {
    //         $user->profile()->create([
    //             "title" => $user->username,
    //         ]);
    //     });
    // }
    // switch to observer

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->orderBy("created_at", "DESC");
    }

    // public function image(): MorphOne
    // {
    //     return $this->morphOne(Image::class, "imageable");
    // }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, "taggable");
    }

    public function location(): MorphMany
    {
        return $this->morphMany(Location::class, "locationable");
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class);
    }

    public function UserVerification(): HasOne
    {
        return $this->hasOne(UserVerification::class);
    }
}