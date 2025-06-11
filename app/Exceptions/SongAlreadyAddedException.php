<?php

declare(strict_types=1);

namespace App\Exceptions;

class SongAlreadyAddedException extends \Exception
{
    protected $message = 'Song with this code already exists or file already exists in storage.';
    protected $code = 400;

    public function __construct(string $code)
    {
        parent::__construct("Song with code {$code} already exists.");
    }
}
