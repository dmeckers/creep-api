<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Stations;

use Spatie\LaravelData\Data;

class SearchStationsRequestData extends Data
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public string $query = '',
        public ?string $sortBy = null,
        public ?string $sortDirection = null,
        public ?string $filterBy = null,
    ) {
    }
}
