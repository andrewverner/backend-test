<?php

declare(strict_types=1);

namespace App\Payment;

use App\Enum\PaymentProcessorsEnum;

readonly class PaymentProcessorRegistry
{
    /**
     * @param iterable<PaymentProcessorAdapterInterface> $processors
     */
    public function __construct(
        private iterable $processors,
    ) {
    }

    public function get(PaymentProcessorsEnum $processorsEnum): PaymentProcessorAdapterInterface
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($processorsEnum)) {
                return $processor;
            }
        }

        throw new \InvalidArgumentException("Unknown payment processor: " . $processorsEnum->value);
    }
}