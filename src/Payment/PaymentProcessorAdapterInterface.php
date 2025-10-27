<?php

declare(strict_types=1);

namespace App\Payment;

use App\Enum\PaymentProcessorsEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.payment_processor')]
interface PaymentProcessorAdapterInterface
{
    /** @psalm-suppress PossiblyUnusedReturnValue */
    public function pay(float $price): bool;

    public function supports(PaymentProcessorsEnum $processorsEnum): bool;
}
