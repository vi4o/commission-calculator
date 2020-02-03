<?php

namespace App;

class CurrencyConverterTest extends TestCase
{
    protected static array $conversionRatesFromEur = [
        'USD' => '1.1497',
        'JPY' => '129.53'
    ];

    private CurrencyConverter $object;

    public function setUp(): void
    {
        $this->object = new CurrencyConverter(self::$conversionRatesFromEur);
    }

    public function testConvertFromEur()
    {
        $money = new Money(5, Currency::EUR);
        $newCurrency = Currency::JPY;

        $expectedResult = new Money('647.65', $newCurrency);
        $actualResult = $this->object->convert($money, $newCurrency);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);
    }

    public function testConvertToEur()
    {
        $money = new Money('647.65', Currency::JPY);
        $newCurrency = Currency::EUR;

        $expectedResult = new Money(5, $newCurrency);
        $actualResult = $this->object->convert($money, $newCurrency);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);
    }

    public function testConvertBetweenNonBaseCurrencies()
    {
        $money = new Money('647.65', Currency::JPY);
        $newCurrency = Currency::USD;

        $expectedResult = new Money('5.75', $newCurrency);
        $actualResult = $this->object->convert($money, $newCurrency);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);
    }
}
