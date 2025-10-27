<?php

declare(strict_types=1);

namespace App\Payment;

use App\Enum\PaymentProcessorsEnum;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

final readonly class PaypalPaymentProcessorAdapter implements PaymentProcessorAdapterInterface
{
    public function __construct(
        private PaypalPaymentProcessor $processor,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function pay(float $price): bool
    {
        $this->processor->pay(price: (int) $price);

        return true;
    }

    public function supports(PaymentProcessorsEnum $processorsEnum): bool
    {
        return $processorsEnum === PaymentProcessorsEnum::PAYPAL;
    }
}
