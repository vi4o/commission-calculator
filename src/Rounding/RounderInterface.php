<?php

namespace App\Rounding;

use App\Money;

interface RounderInterface
{
    public function round(Money $money) : string;
}
