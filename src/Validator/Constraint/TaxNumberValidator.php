<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use App\Enum\CountriesCodeEnum;
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
            CountriesCodeEnum::DE->value => '/^DE\d{9}$/',
            CountriesCodeEnum::IT->value => '/^IT\d{11}$/',
            CountriesCodeEnum::GR->value => '/^GR\d{9}$/',
            CountriesCodeEnum::FR->value => '/^FR[A-Za-z]{2}\d{9}$/'
        ];

        foreach ($patterns as $regex) {
            if (preg_match($regex, $value)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
