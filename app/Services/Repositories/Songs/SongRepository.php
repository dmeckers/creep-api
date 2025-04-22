<?php

declare(strict_types=1);

namespace App\Services\Repositories\Songs;

use App\Http\DataTransferObjects\Songs\StoreSongRequestData;
use App\Models\Song;
use Exception;
use Illuminate\Filesystem\FilesystemManager;

class SongRepository
{
    public function __construct(
        private readonly FilesystemManager $storage,
        private readonly Song $songModel
    ) {
    }

    public function uploadSongToStorage(StoreSongRequestData $data): Song
    {
        $this->checkIfCanUploadSong($data);

        $this->storage->putFile($data->code, $data->file);

        $song = $this->insertSongInDatabase(
            [
                'code' => $data->code,
                'owner_id' => $data->ownerId ?? auth()->id(),
                'artist_id' => $data->artistId,
                'name' => $data->name,
                'file_url' => $this->storage->url($data->code),
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
            $this->storage->exists($data->code)
            || $this->songModel->where('code', '=', $data->code)->exists()
        ) {
            throw new Exception('File already exists');
        }
    }

    public function getStreamedSong(string $code)
    {
        \Log::info('Getting stream for song with code: ' . $code);
        \Log::info('Getting stream for song with code: ' . $code);
        \Log::info('Getting stream for song with code: ' . $code);
        \Log::info('Getting stream for song with code: ' . $code);


        $fileName = $this->storage->files($code)[0];

        return $this->storage->readStream($fileName);
    }
}
