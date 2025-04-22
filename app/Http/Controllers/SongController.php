<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Songs\DeleteSongByCodeRequest;
use App\Http\Requests\Songs\GetSongByCodeRequest;
use App\Http\Requests\Songs\UploadSongRequest;
use App\Http\Resources\SongResource;
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

    public function streamedSong(GetSongByCodeRequest $requset): StreamedResponse
    {
        return response()->stream(
            function () use ($requset) {
                $stream = $this->songRepository->getStreamedSong($requset->code);

                fpassthru($stream);

                fclose($stream);
            },
            200,
            [
                'Content-Type' => 'audio/mpeg',
                'Content-Disposition' => 'inline; filename="' . $requset->code . '.mp3"',
            ]
        );
    }

}
