<?php


namespace App;


class Currency
{
    public const EUR = 'EUR';
    public const USD = 'USD';
    public const JPY = 'JPY';

    public static array $currencies = [
        self::EUR,
        self::USD,
        self::JPY,
    ];

    public static function checkCurrencySupported(string $currency)
    {
        $currency = strtoupper(trim($currency));
        if (array_search($currency, self::$currencies) !== false) {
            return $currency;
        } else {
            throw new \Exception('Unsupported currency used');
        }
    }

}