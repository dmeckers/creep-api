<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public const TELEGRAM_ID       = 'telegram_id';
    public const TELEGRAM_USERNAME = 'telegram_username';
    public const PHOTO_URL         = 'photo_url';
    public const LAST_NAME         = 'last_name';
    public const NAME              = 'name';
    public const EMAIL             = 'email';
    public const PASSWORD          = 'password';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::NAME,
        self::EMAIL,
        self::PASSWORD,
        self::TELEGRAM_ID,
        self::TELEGRAM_USERNAME,
        self::PHOTO_URL,
        self::LAST_NAME,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all stations owned by the user.
     */
    public function stations(): HasMany
    {
        return $this->hasMany(Station::class, 'owner_id');
    }

    /**
     * Get all artists owned by the user.
     */
    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class, 'owner_id');
    }

    /**
     * Get all playlists owned by the user.
     */
    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class, 'owner_id');
    }

    /**
     * Get all songs owned by the user.
     */
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'owner_id');
    }

    /**
     * Get all images owned by the user.
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'owner_id');
    }
    /**
     * @inheritDoc
     */
    public function getAuthPasswordName()
    {
        return 'password';
    }
}
