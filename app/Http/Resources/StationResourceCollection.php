<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StationResourceCollection extends ResourceCollection
{
    /**
     * @var string $collects
     */
    public $collects = StationResource::class;
}