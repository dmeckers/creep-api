<?php

declare(strict_types=1);

namespace App\Http\Requests\Jingles;

use App\Models\Jingle;
use Illuminate\Foundation\Http\FormRequest;

class StreamedJingleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|exists:jingles,' . Jingle::CODE,
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->route('code'),
        ]);
    }

    public function code(): string
    {
        return $this->route('code');
    }
}
