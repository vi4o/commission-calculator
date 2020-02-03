<?php

namespace App;

use Decimal\Decimal;

class CurrencyConverter
{
    protected array $conversionRatesFromEur;

    public function __construct(array $conversionRatesFromEur)
    {
        $this->conversionRatesFromEur = $conversionRatesFromEur;
    }

    public function convert(Money $money, string $currency) : Money
    {
        $baseCurrency = Currency::EUR;
        $newCurrency = Currency::checkCurrencySupported($currency);

        $convertedAmount = null;
        if ($newCurrency === $money->getCurrency()) {
            return $money;
        } elseif ($money->getCurrency() === $baseCurrency) {
            $convertedAmount = $money->getAmount()->mul(new Decimal($this->conversionRatesFromEur[$newCurrency]));
        } elseif ($newCurrency === $baseCurrency) {
            $convertedAmount = $money->getAmount()->div(
                new Decimal($this->conversionRatesFromEur[$money->getCurrency()])
            );
        } else {
            $convertedAmount = $money->getAmount()
                ->div(new Decimal($this->conversionRatesFromEur[$money->getCurrency()]))
                ->mul(new Decimal($this->conversionRatesFromEur[$newCurrency]));
        }

        return new Money($convertedAmount, $newCurrency);
    }
}
