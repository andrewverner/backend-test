<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraint as AppAssert;

final readonly class CalculatePriceRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        public int $product,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[AppAssert\TaxNumber]
        public string $taxNumber,
        #[Assert\Type('string')]
        public ?string $couponCode = null,
    ) {
    }
}
