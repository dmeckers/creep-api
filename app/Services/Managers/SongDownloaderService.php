<?php

declare(strict_types=1);

namespace App\Services\Managers;

use Exception;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ItemNotFoundException;
use SplFileInfo;
use YoutubeDl\Entity\Video;
use YoutubeDl\Options as YoutubeDlOptions;
use YoutubeDl\YoutubeDl;

class SongDownloaderService
{

    /**
     * @throws ItemNotFoundException
     */
    public function downloadFromYoutube(string $url): SplFileInfo
    {
        $youtubeDl = new YoutubeDl();
        $youtubeDl = $youtubeDl->setBinPath(
            config('youtube-dl.bin_path', '/usr/local/bin/yt-dlp')
        );

        // Set up progress callback before initiating download
        $youtubeDl->onProgress(
            static function (?string $progressTarget, string $percentage, string $size, ?string $speed, string $eta, ?string $totalTime): void {
                \Log::info("Download progress", [
                    'file' => $progressTarget,
                    'percentage' => $percentage,
                    'size' => $size,
                    'speed' => $speed ?: 'N/A',
                    'eta' => $eta ?: 'N/A',
                    'total_time' => $totalTime
                ]);
            }
        );

        // The download is synchronous - it will only return after completion
        $collection = $youtubeDl->download(
            YoutubeDlOptions::create()
                ->downloadPath('/var/www/storage/app/songs')
                ->extractAudio(true)
                ->audioFormat('mp3')
                ->audioQuality('0')
                ->cookies(config('youtube-dl.cookies', '/var/www/cookies.txt'))
                ->output('%(title)s.%(ext)s')
                ->url($url)
        );

        /**
         * @var Video $video
         */
        $video = collect($collection->getVideos())
            ->filter(fn(Video $vid) => $vid->getError() === null)
            ->first();

        if ($video !== null) {
            return $video->getFile();
        }

        $error = collect($collection->getVideos())
            ->filter(fn(Video $vid) => $vid->getError() !== null)
            ->first()
                ?->getError();

        throw new Exception(
            'Failed to download song from YouTube: ' . ($error ?? 'Unknown error')
        );
    }
}
