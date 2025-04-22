<?php

declare(strict_types=1);

namespace App\Http\Requests\Jingles;

use App\Http\DataTransferObjects\Jingles\UploadJingleRequestData;
use Illuminate\Foundation\Http\FormRequest;

class UploadJingleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): UploadJingleRequestData
    {
        return resolve(UploadJingleRequestData::class);
    }
}
