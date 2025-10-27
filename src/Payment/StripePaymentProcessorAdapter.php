<?php

declare(strict_types=1);

namespace App\Payment;

use App\Enum\PaymentProcessorsEnum;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

final readonly class StripePaymentProcessorAdapter implements PaymentProcessorAdapterInterface
{
    public function __construct(
        private StripePaymentProcessor $processor
    ) {
    }

    public function pay(float $price): bool
    {
        return $this->processor->processPayment(price: $price);
    }

    public function supports(PaymentProcessorsEnum $processorsEnum): bool
    {
        return $processorsEnum === PaymentProcessorsEnum::STRIPE;
    }
}
