<?php

declare(strict_types=1);

namespace App\Exception;

final class ResourceNotFoundException extends AbstractApplicationException
{
    public static function forField(string $field, string $message = 'Not found', int $status = 422): self
    {
        return new self(sprintf('%s: %s', ucfirst($field), $message), [
            $field => [$message],
        ], $status);
    }

    /**
     * @param array<string, array<int, string>> $errors
     */
    private function __construct(string $message, array $errors, int $status)
    {
        parent::__construct($message, $errors, $status);
    }
}
