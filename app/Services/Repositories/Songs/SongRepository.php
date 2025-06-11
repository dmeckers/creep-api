<?php

declare(strict_types=1);

namespace App\Services\Repositories\Songs;

use App\Http\DataTransferObjects\Songs\GetSongsRequestData;
use App\Http\DataTransferObjects\Songs\StoreSongRequestData;
use App\Http\DataTransferObjects\Songs\UploadFromBotRequestData;
use App\Models\Playlist;
use App\Models\Song;
use App\Services\Repositories\Users\UserRepository;
use Exception;
use getID3;
use GuzzleHttp\Client;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class SongRepository
{
    public function __construct(
        private readonly FilesystemManager $storage,
        private readonly Song $songModel,
        private readonly getID3 $getID3,
        private readonly Client $client,
        private readonly UserRepository $userRepository,
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

    function uploadFromBot(UploadFromBotRequestData $data): Song
    {
        $user = $this->userRepository->firstOrCreate(data: $data->toUserData());

        $this->checkIfCanUploadFromBot($data);

        $filePath = $this->storage->disk('local')->path($data->file_id);

        $this->client->get($data->file_url, [
            'sink' => $filePath,
        ]);

        $songMetaData = $this->getID3->analyze($filePath);
        $duration = $songMetaData['playtime_seconds'];

        $this->storage->disk('s3')->put(
            $data->file_id,
            $this->storage->disk('local')->get($data->file_id)
        );

        $this->storage->disk('local')->delete($data->file_id);

        $song = $this->insertSongInDatabase(
            [
                'code' => $data->file_id,
                'owner_id' => $user->getId(), 
                'artist_id' => null,
                'name' => $data->file_id,
                'file_url' => $this->storage->disk('s3')->url($data->file_id),
                'duration' => $duration,
            ]
        );

        return $song;
    }

    public function checkIfCanUploadFromBot(UploadFromBotRequestData $data): bool
    {
        if (
            false
            || $this->songModel->where('code', '=', $data->file_id)->exists()
            || $this->storage->fileExists($data->file_id)
            || $this->storage->directoryExists($data->file_id)
        ) {
            throw new Exception('File already exists');
        }

        return true;
    }
}
