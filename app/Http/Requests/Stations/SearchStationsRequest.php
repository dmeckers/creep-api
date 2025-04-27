<?php

declare(strict_types=1);

namespace App\Http\Requests\Stations;

use App\Http\DataTransferObjects\Stations\SearchStationsRequestData;
use App\Http\Resources\StationResourceCollection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchStationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): SearchStationsRequestData
    {
        return resolve(SearchStationsRequestData::class);
    }

    public function responseResource(LengthAwarePaginator $stations): StationResourceCollection
    {
        return new StationResourceCollection($stations);
    }
}
