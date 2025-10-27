<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatorService
{
    private array $errors = [];

    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function validate(object $dto): bool
    {
        $this->errors = [];

        $violations = $this->validator->validate(value: $dto);

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $field = $violation->getPropertyPath() ?: 'body';
                $this->errors[$field][] = $violation->getMessage();
            }

            return false;
        }

        return true;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
