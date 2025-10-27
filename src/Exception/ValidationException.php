<?php

declare(strict_types=1);

namespace App\Exception;

final class ValidationException extends AbstractApplicationException
{
    /**
     * @param array<string, array<int, string>> $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct('Validation failed', $errors, 422);
    }
}
