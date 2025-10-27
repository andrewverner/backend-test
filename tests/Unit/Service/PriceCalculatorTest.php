<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Coupon;
use App\Enum\CountriesCodeEnum;
use App\Enum\CouponTypeEnum;
use App\Service\PriceCalculator;
use PHPUnit\Framework\TestCase;

final class PriceCalculatorTest extends TestCase
{
    private PriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    public function testCalculateWithoutCouponAppliesTax(): void
    {
        $price = $this->calculator->calculate(100, CountriesCodeEnum::DE, null);

        self::assertSame(119.0, $price);
    }

    public function testCalculateWithPercentCoupon(): void
    {
        $coupon = (new Coupon())
            ->setType(CouponTypeEnum::PERCENT_DISCOUNT)
            ->setValue(10);

        $price = $this->calculator->calculate(100, CountriesCodeEnum::DE, $coupon);

        self::assertSame(107.1, $price);
    }

    public function testCalculateWithFixedCouponDoesNotGoBelowZero(): void
    {
        $coupon = (new Coupon())
            ->setType(CouponTypeEnum::FIXED_DISCOUNT)
            ->setValue(150);

        $price = $this->calculator->calculate(100, CountriesCodeEnum::DE, $coupon);

        self::assertSame(0.0, $price);
    }
}
