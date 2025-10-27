<?php

declare(strict_types=1);

namespace App\Trait;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidationTrait
{
    private function validationErrors(ValidatorInterface $v, object $dto): ?JsonResponse
    {
        $violations = $v->validate($dto);
        if (count($violations) === 0) return null;

        $errors = [];
        foreach ($violations as $violation) {
            $field = $violation->getPropertyPath() ?: 'body';
            $errors[$field][] = $violation->getMessage();
        }
        return new JsonResponse(['errors' => $errors], 422);
    }
}
