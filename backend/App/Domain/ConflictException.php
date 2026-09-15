<?php

declare(strict_types=1);

namespace App\Domain;

class ConflictException extends \RuntimeException
{
    public function __construct(string $message = 'Resource already exists')
    {
        parent::__construct($message, 409);
    }
}
