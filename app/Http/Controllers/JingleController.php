<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Jingles\DeleteJingleByCodeRequest;
use App\Http\Requests\Jingles\GetAllJinglesRequest;
use App\Http\Requests\Jingles\GetJingleByCodeRequest;
use App\Http\Requests\Jingles\StreamedJingleRequest;
use App\Http\Requests\Jingles\StreamRandomJingleRequest;
use App\Http\Requests\Jingles\UploadJingleRequest;
use App\Http\Resources\JingleResourceCollection;
use App\Models\Jingle;
use App\Services\Repositories\Jingles\JingleRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JingleController extends Controller
{
    public function __construct(
        private readonly JingleRepository $jingleRepository
    ) {
    }

    public function uploadJingle(UploadJingleRequest $request): JsonResponse
    {
        $this->jingleRepository->uploadJingleToStorage($request->data());

        return response()->json([], 204);
    }

    public function getJingleByCode(GetJingleByCodeRequest $requst): JsonResponse
    {
        $jingle = $this->jingleRepository->getByCode($requst->code());

        return response()->json($jingle);
    }

    public function deleteJingleByCode(DeleteJingleByCodeRequest $request): JsonResponse
    {
        $this->jingleRepository->deleteByCode($request->code());

        return response()->json([], 204);
    }

    public function streamedJingle(StreamedJingleRequest $request): StreamedResponse
    {
        $jingle = $this->jingleRepository->getByCode($request->code());

        return response()->stream(
            function () use ($jingle) {
                $stream = $this->jingleRepository->getStreamedJingle($jingle);

                fpassthru($stream);

                fclose($stream);
            },
            200,
            [
                'Content-Type' => 'audio/mpeg',
                'Content-Disposition' => 'inline; filename="' . $jingle->{Jingle::CODE} . '.mp3"',
            ]
        );
    }

    public function streamRandomJingle(StreamRandomJingleRequest $request): StreamedResponse
    {
        $jingle = $this->jingleRepository->getRandomJingle($request->stationId());

        return response()->stream(
            function () use ($jingle) {
                $stream = $this->jingleRepository->getStreamedJingle($jingle);

                fpassthru($stream);

                fclose($stream);
            },
            200,
            [
                'Content-Type' => 'audio/mpeg',
                'Content-Disposition' => 'inline; filename="' . $jingle->{Jingle::CODE} . '.mp3"',
            ]
        );
    }

    public function getAllJingles(GetAllJinglesRequest $request): JingleResourceCollection
    {
        return $request->resourceResponse(
            $this->jingleRepository->getAllJingles($request->stationId())
        );
    }
}
