<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_live',
        'mount_point',
        'stream_url',
        'stream_type',
        'stream_format',
        'stream_bitrate',
        'stream_sample_rate',
        'owner_id',
        'is_public',
        'description',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, StationPlaylistPivot::class)->withPivot(
            [
                'station_id',
                'playlist_id',
                'is_playing',
            ]
        );
    }

    public function playingPlaylist(): ?Playlist
    {
        return $this->playlists()->wherePivot('is_playing', true)->first();
    }

    public function setPlayingPlaylist(Playlist $playlist): static
    {
        $this->playlists()->newPivotQuery()->update(['is_playing' => false]);

        $this->playlists()->syncWithoutDetaching([$playlist->getId() => ['is_playing' => true]]);

        return $this;
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

    public function isLive(): bool
    {
        return $this->getAttribute('is_live');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getMountPoint(): string
    {
        return $this->getAttribute('mount_point');
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function setIsLive(bool $isLive): static
    {
        $this->setAttribute('is_live', $isLive);

        return $this;
    }
}
