<?php

declare(strict_types=1);

namespace App\Services\Repositories\Jingles;

use App\Http\DataTransferObjects\Jingles\UploadJingleRequestData;
use App\Models\Jingle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\FilesystemManager;

class JingleRepository
{
    public const STORAGE_PATH = 'jingles/';

    public function __construct(
        private readonly Jingle $jingleModel,
        private readonly FilesystemManager $storage,
    ) {
    }

    public function getByCode(string $code): Jingle
    {
        return $this->jingleModel->where(Jingle::CODE, '=', $code)->firstOrFail();
    }

    public function deleteByCode(string $code): void
    {
        $jingle = $this->getByCode($code);

        $this->storage->delete(self::STORAGE_PATH . $jingle->getStationId() . '/' . $jingle->code);

        $jingle->delete();
    }

    public function uploadJingleToStorage(UploadJingleRequestData $data): Jingle
    {
        $this->storage->putFileAs(self::STORAGE_PATH . $data->station_id . '/', $data->file, $data->code);

        return $this->insertJingleInDatabase(
            [
                Jingle::CODE => $data->code,
                Jingle::STATION_ID => $data->station_id,
            ]
        );
    }

    public function insertJingleInDatabase(array $data): Jingle
    {
        return $this->jingleModel->create($data);
    }

    public function getStreamedJingle(Jingle $jingle)
    {
        return $this->storage->readStream(self::STORAGE_PATH . $jingle->getStationId() . '/' . $jingle->getCode());
    }

    public function getRandomJingle(int $stationId): Jingle
    {
        return $this->jingleModel
            ->where(Jingle::STATION_ID, '=', $stationId)
            ->inRandomOrder()
            ->firstOrFail();
    }

    public function getAllJingles(int $stationId): Collection
    {
        return $this->jingleModel->where(Jingle::STATION_ID, '=', $stationId)->get();
    }
}
