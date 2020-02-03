<?php

namespace App\Rounding;


use App\Currency;
use App\Money;
use App\TestCase;

class CeilToSmallestCurrencyItemRounderTest extends TestCase
{
    protected CeilToSmallestCurrencyItemRounder $rounder;

    public function setUp(): void
    {
        $this->rounder = new CeilToSmallestCurrencyItemRounder();
    }

    public function testRoundEur()
    {
        $expectedAfterRounding = '0.01';

        $money = new Money('0.001', Currency::EUR);
        $money1 = new Money('0.005', Currency::EUR);

        $rounded = $this->rounder->round($money);
        $rounded1 = $this->rounder->round($money1);

        $this->assertEquals($expectedAfterRounding, $rounded);
        $this->assertEquals($expectedAfterRounding, $rounded1);
    }

    public function testRoundJpy()
    {
        $expectedAfterRounding = '1';

        $money = new Money('0.001', Currency::JPY);
        $money1 = new Money('0.1', Currency::JPY);

        $rounded = $this->rounder->round($money);
        $rounded1 = $this->rounder->round($money1);

        $this->assertEquals($expectedAfterRounding, $rounded);
        $this->assertEquals($expectedAfterRounding, $rounded1);
    }
}
