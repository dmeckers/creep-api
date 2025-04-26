<?php

declare(strict_types=1);

namespace App\Enums;

enum AppEnvEnum: string
{
    case LOCAL = 'local';
    case STAGING = 'staging';
    case PRODUCTION = 'production';
}
