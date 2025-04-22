<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class JingleResourceCollection extends ResourceCollection
{
    public $collects = JingleResource::class;
}