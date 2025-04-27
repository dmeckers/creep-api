<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetPlaylistSongsRequestResource extends JsonResource
{
    /**
     * @var \App\Models\Playlist
     */
    public $resource;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'songs' => $this->resource->getSongsPaginated() ?? $this->resource->songs
        ];
    }
}
