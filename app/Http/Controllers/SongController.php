<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\SongAlreadyAddedException;
use App\Http\Requests\Songs\DeleteSongRequest;
use App\Http\Requests\Songs\GetSongByCodeRequest;
use App\Http\Requests\Songs\GetSongRequest;
use App\Http\Requests\Songs\GetSongsRequest;
use App\Http\Requests\Songs\UploadFromBotRequest;
use App\Http\Requests\Songs\UploadSongFromYoutubeRequest;
use App\Http\Requests\Songs\UploadSongRequest;
use App\Http\Resources\SongResource;
use App\Http\Resources\SongResourceCollection;
use App\Services\Repositories\Songs\SongRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SongController extends Controller
{
    public function __construct(
        private readonly SongRepository $songRepository
    ) {
    }

    public function upload(UploadSongRequest $request): SongResource
    {
        try {
            return $request->resourceResponse(
                $this->songRepository->uploadSongToStorage(
                    $request->data()
                )
            );
        } catch (\Throwable $th) {
            \Log::error('Error uploading song', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            throw $th;
        }
    }

    public function findOrFail(GetSongRequest $request): SongResource
    {
        return $request->resourceResponse(
            $this->songRepository->findOrFail($request->data()->id)
        );
    }

    public function delete(DeleteSongRequest $request): JsonResponse
    {
        $this->songRepository->delete($request->data()->id);

        return response()->json([], 204);
    }

    public function streamedSong(GetSongByCodeRequest $request): StreamedResponse
    {
        $stream = $this->songRepository->getStreamedSong($request->code);
        $contents = stream_get_contents($stream);
        fclose($stream);

        // Create a temporary file that we can access directly
        $tempFile = tempnam(sys_get_temp_dir(), 'song_');
        file_put_contents($tempFile, $contents);

        $filesize = filesize($tempFile);
        $start = 0;
        $end = $filesize - 1;
        $status = 200;
        $headers = [
            'Content-Type' => 'audio/mpeg',
            'Content-Disposition' => 'inline; filename="' . $request->code . '.mp3"',
            'Accept-Ranges' => 'bytes',
        ];

        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
            if (preg_match('/bytes=(\d+)-(\d+)?/', $range, $matches)) {
                $start = intval($matches[1]);
                if (isset($matches[2])) {
                    $end = intval($matches[2]);
                }
                $status = 206;
            }

            $length = $end - $start + 1;

            $headers += [
                'Content-Range' => "bytes $start-$end/$filesize",
                'Content-Length' => $length,
            ];
        } else {
            $headers['Content-Length'] = $filesize;
        }

        return response()->stream(function () use ($tempFile, $start, $end) {
            $chunkSize = 1024 * 8;
            $handle = fopen($tempFile, 'rb');
            fseek($handle, $start);
            $bytesToOutput = $end - $start + 1;

            while (!feof($handle) && $bytesToOutput > 0) {
                $readLength = min($chunkSize, $bytesToOutput);
                echo fread($handle, $readLength);
                flush();
                $bytesToOutput -= $readLength;
            }

            fclose($handle);
            // Clean up the temporary file
            @unlink($tempFile);
        }, $status, $headers);
    }


    public function getSongs(GetSongsRequest $requst): SongResourceCollection
    {
        $songs = $this->songRepository->getSongs($requst->data());

        return SongResourceCollection::make($songs);
    }

    public function uploadFromBot(UploadFromBotRequest $request)
    {
        try {
            $request->response(
                $this->songRepository->uploadFromBot(
                    $request->data()
                )
            );
        } catch (SongAlreadyAddedException $th) {
            return response()->json([
                'message' => 'Song with this code already exists or file already exists in storage.'
            ], 400);
        }
    }
}
