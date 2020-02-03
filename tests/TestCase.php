<?php
namespace App;

use Decimal\Decimal;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function assertMoneyAreEqual(Money $expected, Money $actual)
    {
        $this->assertEquals(
            $expected->getAmount()->toFixed(2, false, Decimal::ROUND_CEILING),
            $actual->getAmount()->toFixed(2, false, Decimal::ROUND_CEILING),
            'Money amounts are not equal!'
        );
        $this->assertEquals($expected->getCurrency(), $actual->getCurrency(), 'Currencies are not the same!');
    }
}