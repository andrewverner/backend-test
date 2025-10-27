<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNumberValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value || !is_string($value)) {
            return;
        }

        $patterns = [
            'DE' => '/^DE\d{9}$/',
            'IT' => '/^IT\d{11}$/',
            'GR' => '/^GR\d{9}$/',
            'FR' => '/^FR[A-Za-z]{2}\d{9}$/'
        ];

        foreach ($patterns as $regex) {
            if (preg_match($regex, $value)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
