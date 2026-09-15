<?php

declare(strict_types=1);

namespace App\Domain;

class ValidationException extends \InvalidArgumentException
{
    /** @var array<string,string|string[]> */
    private array $errors;

    /**
     * @param array<string,string|string[]> $errors
     */
    public function __construct(array $errors, string $message = 'Validation failed')
    {
        $this->errors = $errors;
        parent::__construct($message, 422);
    }

    /**
     * @return array<string,string|string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
