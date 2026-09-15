<?php

declare(strict_types=1);

namespace App\Domain;

class NotFoundException extends \RuntimeException
{
    public function __construct(string $message = 'Resource not found')
    {
        parent::__construct($message, 404);
    }
}
