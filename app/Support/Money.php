<?php

namespace App\Support;

class Money
{
    // 250000 -> "$ 250.000"
    public static function format(float|int|string|null $amount): string
    {
        return '$ '.number_format((float) $amount, 0, ',', '.');
    }
}
