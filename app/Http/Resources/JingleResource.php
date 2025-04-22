<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JingleResource extends JsonResource
{
    /**
     * @var \App\Models\Jingle
     */
    public $resource;

    public function toArray($request): array
    {
        return $this->resource->toArray();
    }
}
