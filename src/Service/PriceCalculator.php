<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Coupon;
use App\Enum\CountriesCodeEnum;
use App\Enum\CouponTypeEnum;

class PriceCalculator
{
    public function calculate(int $price, CountriesCodeEnum $countryCode, ?Coupon $coupon): float
    {
        if ($coupon) {
            $discount = match ($coupon->getType()) {
                CouponTypeEnum::PERCENT_DISCOUNT => $price * ($coupon->getValue() / 100),
                CouponTypeEnum::FIXED_DISCOUNT => $coupon->getValue(),
            };

            $price -= $discount;
        }

        $price = max(0, $price);

        return round($price * (1 + $countryCode->getTaxAmount()), 2);
    }
}
