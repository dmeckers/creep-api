<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Songs\DeleteSongByCodeRequest;
use App\Http\Requests\Songs\GetSongByCodeRequest;
use App\Http\Requests\Songs\GetSongsRequest;
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

    public function getByCode(GetSongByCodeRequest $requset): SongResource
    {
        return $requset->resourceResponse(
            $this->songRepository->getByCode($requset->code)
        );
    }

    public function deleteByCode(DeleteSongByCodeRequest $requset): JsonResponse
    {
        $this->songRepository->deleteByCode($requset->code);

        return response()->json([], 204);
    }

    public function streamedSong(GetSongByCodeRequest $request): StreamedResponse
    {
        $stream = $this->songRepository->getStreamedSong($request->code);
        $path = stream_get_meta_data($stream)['uri'];
        $filesize = filesize($path);

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

        return response()->stream(function () use ($path, $start, $end) {
            $chunkSize = 1024 * 8;
            $handle = fopen($path, 'rb');
            fseek($handle, $start);
            $bytesToOutput = $end - $start + 1;

            while (!feof($handle) && $bytesToOutput > 0) {
                $readLength = min($chunkSize, $bytesToOutput);
                echo fread($handle, $readLength);
                flush();
                $bytesToOutput -= $readLength;
            }

            fclose($handle);
        }, $status, $headers);
    }


    public function getSongs(GetSongsRequest $requst): SongResourceCollection
    {
        $songs = $this->songRepository->getSongs($requst->data());

        return SongResourceCollection::make($songs);
    }
}
