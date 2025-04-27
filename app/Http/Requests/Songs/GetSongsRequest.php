<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\GetSongsRequestData;
use Illuminate\Foundation\Http\FormRequest;

class GetSongsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): GetSongsRequestData
    {
        return resolve(GetSongsRequestData::class);
    }
}
