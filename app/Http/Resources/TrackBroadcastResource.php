<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\TrackBroadcast;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackBroadcastResource extends JsonResource
{
    /**
     * @var TrackBroadcast
     */
    public $resource;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'start_at' => $this->resource->getBroadcastedAt(),
            'song' => new SongResource($this->resource->relatedSong()),
        ];
    }
}
