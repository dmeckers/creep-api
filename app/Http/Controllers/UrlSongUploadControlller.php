<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Songs\UploadSongFromYoutubeRequest;
use App\Http\Resources\SongResource;
use App\Jobs\DownloadFromYoutubeSongJob;
use Illuminate\Http\JsonResponse;

class UrlSongUploadControlller extends Controller
{
    public function __construct(
    ) {
    }

    public function uploadFromYoutube(UploadSongFromYoutubeRequest $request): JsonResponse
    {
        DownloadFromYoutubeSongJob::dispatch($request->data());
     
        return $request->response();
    }

    // public function uploadFromVkontakte(UploadSongFromVkontakteRequest $request): SongResource
    // {
    //     // return $request->resourceResponse(
    //     //     $this->songRepository->uploadSongFromVkontakte(
    //     //         $request->data()
    //     //     )
    //     // );
    // }
}