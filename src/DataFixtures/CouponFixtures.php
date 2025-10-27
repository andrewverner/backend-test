<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Coupon;
use App\Enum\CouponTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $coupons = [
            ['code' => 'P25', 'type' => CouponTypeEnum::PERCENT_DISCOUNT, 'value' => 25],
            ['code' => 'P60', 'type' => CouponTypeEnum::PERCENT_DISCOUNT, 'value' => 60],
            ['code' => 'F2K', 'type' => CouponTypeEnum::FIXED_DISCOUNT, 'value' => 2000],
            ['code' => 'F15K', 'type' => CouponTypeEnum::FIXED_DISCOUNT, 'value' => 15000],
        ];

        foreach ($coupons as $coupon) {
            $coupon = (new Coupon())
                ->setCode($coupon['code'])
                ->setType($coupon['type'])
                ->setValue($coupon['value'])
            ;
            $manager->persist($coupon);
        }

        $manager->flush();
    }
}
