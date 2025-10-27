<?php

declare(strict_types=1);

namespace App\Exception;

abstract class AbstractApplicationException extends \DomainException implements ApplicationExceptionInterface
{
    /**
     * @param array<string, array<int, string>> $errors
     */
    public function __construct(
        string $message,
        private readonly array $errors,
        private readonly int $status
    ) {
        parent::__construct($message);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
