<?php

declare(strict_types=1);

namespace App\Enum;

enum PaymentProcessorsEnum: string
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';

    public static function getCases(): array
    {
        return [
            self::PAYPAL->value,
            self::STRIPE->value,
        ];
    }
}
