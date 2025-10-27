<?php

declare(strict_types=1);

namespace App\Enum;

enum CouponTypeEnum: string
{
    case FIXED_DISCOUNT = 'fixed_discount';
    case PERCENT_DISCOUNT = 'percent_discount';
}
