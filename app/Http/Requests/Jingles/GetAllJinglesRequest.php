<?php
declare(strict_types=1);

namespace App\Http\Requests\Jingles;

use App\Http\Resources\JingleResourceCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;

class GetAllJinglesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'station_id' => 'required|integer|exists:stations,id',
        ];
    }

    public function stationId(): int
    {
        return (int) $this->route('station_id');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'station_id' => $this->route('station_id'),
        ]);
    }

    public function resourceResponse(Collection $collection): JingleResourceCollection
    {
        return JingleResourceCollection::make($collection);
    }
}
