<?php

declare(strict_types=1);

namespace App\Services\Repositories\Songs;

use App\Http\DataTransferObjects\Songs\GetSongsRequestData;
use App\Http\DataTransferObjects\Songs\StoreSongRequestData;
use App\Models\Playlist;
use App\Models\Song;
use Exception;
use getID3;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class SongRepository
{
    public function __construct(
        private readonly FilesystemManager $storage,
        private readonly Song $songModel,
        private readonly getID3 $getID3
    ) {
    }

    public function uploadSongToStorage(StoreSongRequestData $data): Song
    {
        $this->checkIfCanUploadSong($data);

        $songMetaData = $this->getID3->analyze($data->file->getRealPath());

        $duration = $songMetaData['playtime_seconds'];

        $this->storage->putFile($data->code, $data->file);

        $song = $this->insertSongInDatabase(
            [
                'code' => $data->code ?? $data->file->hashName(),
                'owner_id' => $data->ownerId ?? auth()->id(),
                'artist_id' => $data->artistId,
                'name' => $data->name ?? $data->file->getClientOriginalName(),
                'file_url' => $this->storage->url($data->code),
                'duration' => $duration,
            ]
        );

        if ($data->playlistId) {
            $song->playlists()->attach($data->playlistId);
        }

        return $song;
    }

    public function insertSongInDatabase(array $data): Song
    {
        return $this->songModel->create($data);
    }

    public function getByCode(string $code): Song
    {
        return $this->songModel->where('code', '=', $code)->firstOrFail();
    }

    public function deleteByCode(string $code): void
    {
        $song = $this->getByCode($code);

        $this->storage->delete($song->code);

        $song->delete();
    }

    private function checkIfCanUploadSong(StoreSongRequestData $data): void
    {
        if (
            false
            || $this->songModel->where('code', '=', $data->code)->exists()
            || $this->storage->fileExists($data->code)
            || $this->storage->directoryExists($data->code)
        ) {
            throw new Exception('File already exists');
        }
    }

    public function getStreamedSong(string $code)
    {
        $fileName = $this->storage->files($code)[0];

        return $this->storage->readStream($fileName);
    }

    public function getSongs(GetSongsRequestData $data): LengthAwarePaginator
    {
        return $this->songModel->latest()
            ->when(
                $data->exclude_playlist_id,
                fn($query, $excludeFromPlaylistId) => $query->whereDoesntHave(
                    relation: Song::PLAYLISTS_RELATION,
                    callback: fn($query) => $query->where(
                        column: Playlist::TABLE_NAME . '.' . Song::ID,
                        operator: '=',
                        value: $excludeFromPlaylistId
                    )
                )
            )
            ->when(
                $data->name,
                fn($query, $name) => $query->where(
                    column: 'name',
                    operator: 'like',
                    value: '%' . $name . '%'
                )
            )
            ->where(
                column: Song::OWNER_ID,
                operator: '=',
                value: $data->owner_id ?? auth()->id()
            )
            ->paginate(
                perPage: $data->per_page,
                page: $data->page,
            );
    }
}
