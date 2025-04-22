<?php

declare(strict_types=1);

namespace App\Http\Requests\Jingles;

use App\Models\Jingle;
use Illuminate\Foundation\Http\FormRequest;

class StreamRandomJingleRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'station_id' => (int) $this->route('station_id'),
        ]);
    }

    public function stationId(): int
    {
        return (int) $this->route('station_id');
    }
}
