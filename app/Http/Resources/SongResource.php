<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
{
    /**
     * @var Song
     */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'artist' => $this->resource->getArtist(),
            'fileUrl' => $this->resource->getCode(),
        ];
    }
}
