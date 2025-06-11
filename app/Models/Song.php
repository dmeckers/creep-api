<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'artist_id',
        'description',
        'owner_id',
        'file_url',
        'duration',
    ];

    protected $casts = [
        'duration' => 'float',
    ];

    public const TABLE_NAME = 'songs';

    public const ID              = 'id';
    public const NAME            = 'name';
    public const CODE            = 'code';
    public const ARTIST_ID       = 'artist_id';
    public const DESCRIPTION     = 'description';
    public const OWNER_ID        = 'owner_id';
    public const FILE_URL        = 'file_url';
    public const DURATION        = 'duration';
    public const ARTIST_RELATION = 'artist';

    public const PLAYLISTS_RELATION = 'playlists';

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Playlist::class,
            table: PlaylistSongPivot::class,
            foreignPivotKey: 'song_id',
            relatedPivotKey: 'playlist_id'
        );
    }

    public function genres(): MorphMany
    {
        return $this->morphMany(Genre::class, 'genreable', 'genreables');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function like(): MorphOne
    {
        return $this->morphOne(Like::class, 'likeable');
    }

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getArtist(): string
    {
        return $this->artist()->first()?->getName() ?? 'Unknown Artist';
    }

    public function getFileUrl(): string
    {
        return $this->getAttribute('file_url');
    }

    public function getCode(): string
    {
        return $this->getAttribute('code');
    }

    public function getDuration(): float
    {
        return $this->getAttribute('duration');
    }
}
