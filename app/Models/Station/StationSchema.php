<?php

declare(strict_types=1);

namespace App\Models\Station;

use App\Models\Genre;
use App\Models\Image;
use App\Models\Like;
use App\Models\Playlist;
use App\Models\StationPlaylistPivot;
use App\Models\StationQueue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class StationSchema extends Model
{
    use HasFactory;

    public const TABLE_NAME  = 'stations';
    public const ID          = 'id';
    public const NAME        = 'name';
    public const IS_LIVE     = 'is_live';
    public const MOUNT_POINT = 'mount_point';
    public const STREAM_URL  = 'stream_url';
    public const OWNER_ID    = 'owner_id';
    public const IS_PUBLIC   = 'is_public';
    public const DESCRIPTION = 'description';

    protected $fillable = [
        'name',
        'is_live',
        'mount_point',
        'owner_id',
        'is_public',
        'description',
        'stream_url',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'is_public' => 'boolean',
    ];

    public const PLAYLIST_RELATION      = 'playlists';
    public const STATION_QUEUE_RELATION = 'stationQueue';

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, StationPlaylistPivot::class)
            ->withPivot(
                [
                    'station_id',
                    'playlist_id',
                    StationPlaylistPivot::IS_PLAYING,
                ]
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

    public function isLive(): bool
    {
        return $this->getAttribute('is_live');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getIsPublic(): bool
    {
        return $this->getAttribute(self::IS_PUBLIC);
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

    public function stationQueue(): HasOne
    {
        return $this->hasOne(
            StationQueue::class,
            StationQueue::STATION_ID,
            self::ID
        );
    }

    public function playingPlaylist(): ?Playlist
    {
        return $this->playlists()->wherePivot(StationPlaylistPivot::IS_PLAYING, '=', true)->first();
    }

    public function setPlayingPlaylist(Playlist $playlist): static
    {
        $this->playlists()->newPivotQuery()->update([StationPlaylistPivot::IS_PLAYING => false]);

        $this->playlists()->syncWithoutDetaching([$playlist->getId() => [StationPlaylistPivot::IS_PLAYING => true]]);

        return $this;
    }

    public function isLooped(): bool
    {
        return true;
    }
}
