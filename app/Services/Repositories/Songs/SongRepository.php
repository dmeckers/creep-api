<?php

declare(strict_types=1);

namespace App\Services\Repositories\Songs;

use App\Exceptions\SongAlreadyAddedException;
use App\Http\DataTransferObjects\Songs\GetSongsRequestData;
use App\Http\DataTransferObjects\Songs\StoreSongRequestData;
use App\Http\DataTransferObjects\Songs\UploadFromBotRequestData;
use App\Models\Playlist;
use App\Models\Song;
use App\Services\Helpers\CodeGenerator;
use App\Services\Repositories\Users\UserRepository;
use Exception;
use getID3;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $songMetaData = $this->getID3->analyze($data->file->getRealPath());

        $duration = $songMetaData['playtime_seconds'];
        $fileSize = $songMetaData['filesize'];

        $code = CodeGenerator::generateCode(
            fileSize: $fileSize,
            fileName: $data->name,
        );

        if (!$this->canUploadSong($code)) {
            throw new SongAlreadyAddedException('Song with this code already exists or file already exists in storage.');
        }

        $this->storage->putFile($code, $data->file);

        $song = $this->insertSongInDatabase(
            [
                'code' => $code,
                'owner_id' => $data->ownerId ?? auth()->id(),
                'artist_id' => $data->artistId,
                'name' => $data->name ?? $data->file->getClientOriginalName(),
                'file_url' => $this->storage->url($code),
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

    /**
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $songId): Song
    {
        return $this->songModel->findOrFail($songId);
    }

    public function delete(int $songId): void
    {
        $song = $this->findOrFail($songId);

        $this->storage->delete($song->getCode());

        $song->delete();
    }

    private function canUploadSong(string $code): bool
    {
        $cant = false
            || $this->songModel->where('code', '=', $code)->exists()
            || $this->storage->fileExists($code)
            || $this->storage->directoryExists($code);

        return !$cant;
    }

    public function getStreamedSong(string $code)
    {
        $savedFromLocal = $this->storage->files($code);

        if (!empty($savedFromLocal)) {
            $fileName = $savedFromLocal[0];

            return $this->storage->readStream($fileName);
        }

        return $this->storage->readStream($code);
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

        $filePath = $this->storage->disk('local')->path($data->file_id);

        $deleteFromLocalStorage = fn() => $this->storage->disk('local')->delete($data->file_id);

        $this->client->get($data->file_url, [
            'sink' => $filePath,
        ]);

        $code = CodeGenerator::generateCode(
            fileSize: filesize($filePath),
            fileName: $data->filename,
        );

        if (!$this->canUploadSong($code)) {
            $deleteFromLocalStorage();

            throw new SongAlreadyAddedException('Song with this code already exists or file already exists in storage.');
        }

        $songMetaData = $this->getID3->analyze($filePath);
        $duration = $songMetaData['playtime_seconds'];

        $this->storage->disk('s3')->put(
            $code,
            $this->storage->disk('local')->get($data->file_id)
        );

        $deleteFromLocalStorage();

        $song = $this->insertSongInDatabase(
            [
                'code' => $code,
                'owner_id' => $user->getId(),
                'artist_id' => null,
                'name' => $data->filename,
                'file_url' => $this->storage->disk('s3')->url($code),
                'duration' => $duration,
            ]
        );

        return $song;
    }
}
