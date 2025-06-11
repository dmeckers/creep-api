<?php

declare(strict_types=1);

namespace App\Services\Helpers;

class CodeGenerator
{
    public static function generateCode(
        int $fileSize,
        string $fileName,
    ): string {
        return base64_encode("{$fileSize}-{$fileName}");
    }
}
