<?php
namespace App;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function assertMoneyAreEqual(Money $expected, Money $actual)
    {
        $this->assertEquals(
            $expected->getAmount()->toFixed(2),
            $actual->getAmount()->toFixed(2),
            'Money amounts are not equal!'
        );
        $this->assertEquals($expected->getCurrency(), $actual->getCurrency(), 'Currencies are not the same!');
    }
}