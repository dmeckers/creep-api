<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Station\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Pagination\LengthAwarePaginator;

class Playlist extends Model
{
    use HasFactory;

    public const TABLE_NAME = 'playlists';



    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];

    private ?LengthAwarePaginator $songsPaginated = null;

    public const ID          = 'id';
    public const NAME        = 'name';
    public const OWNER_ID    = 'owner_id';
    public const DESCRIPTION = 'description';

    public const STATIONS_RELATION = 'stations';
    public const SONGS_RELATION    = 'songs';

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Song::class,
            table: PlaylistSongPivot::class,
            foreignPivotKey: 'playlist_id',
            relatedPivotKey: 'song_id'
        );
    }

    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class, StationPlaylistPivot::class);
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

    public function setSongsPaginated(LengthAwarePaginator $songs): static
    {
        $this->songsPaginated = $songs;

        return $this;
    }

    public function getSongsPaginated(): ?LengthAwarePaginator
    {
        return $this->songsPaginated;
    }
}
