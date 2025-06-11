<?php

declare(strict_types=1);

namespace App\Enums;

enum RouteValidationEnum: string
{
    case ID = '[0-9]+';
    case HASH = '[a-z0-9]+';
}
