<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\UrlSongDowloadFailedEvent;
use App\Events\UrlSongDowloadSucceededEvent;
use App\Http\DataTransferObjects\Songs\UploadSongFromYoutubeRequestData;
use App\Services\Managers\SongDownloaderService;
use App\Services\Repositories\Songs\SongRepository;
use Exception;
use getID3;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\ItemNotFoundException;

class DownloadFromYoutubeSongJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly UploadSongFromYoutubeRequestData $data,

    ) {
    }

    public function handle(
        SongDownloaderService $downloader,
        getID3 $getID3,
        FilesystemManager $storage,
        SongRepository $songRepository,
    ): void {

        try {
            $splFileInfo = $downloader->downloadFromYoutube($this->data->youtubeUrl);

            $songMetaData = $getID3->analyze($splFileInfo->getRealPath());

            $duration = $songMetaData['playtime_seconds'];

            \Log::info('Song metadata', [
                'duration' => $duration,
                'file' => $splFileInfo->getRealPath(),
            ]);

            $code = base64_encode(hash('sha256', data: $splFileInfo->getFilename()));

            $storage->putFileAs(
                $code,
                $splFileInfo,
                $splFileInfo->getFilename(),
            );

            $songRepository->insertSongInDatabase(
                [
                    'code' => $code,
                    'owner_id' => $this->data->userId,
                    'name' => $splFileInfo->getFilename(),
                    'file_url' => $storage->url($code),
                    'duration' => $duration,
                ]
            );

            $storage->disk('local')->deleteDirectory('songs');

            broadcast(
                new UrlSongDowloadSucceededEvent(
                    $this->data->youtubeUrl,
                    $this->data->userId
                )
            );
        } catch (ItemNotFoundException $th) {
            \Log::info('Download failed', [
                'error' => $th->getMessage(),
                'youtube_url' => $this->data->youtubeUrl,
                'user_id' => $this->data->userId,
            ]);

            broadcast(new UrlSongDowloadFailedEvent(
                $this->data->youtubeUrl,
                $th->getMessage(),
                $this->data->userId
            ));
        }
    }
}
