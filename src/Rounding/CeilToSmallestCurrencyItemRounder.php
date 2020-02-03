<?php


namespace App\Rounding;


use App\Currency;
use App\Money;
use Decimal\Decimal;

class CeilToSmallestCurrencyItemRounder implements RounderInterface
{

    public function round(Money $money): string
    {
        return $money->getAmount()->toFixed(
            Currency::$currencyPrecisions[$money->getCurrency()],
            false,
            Decimal::ROUND_CEILING
        );
    }
}