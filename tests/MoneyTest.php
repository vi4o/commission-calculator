<?php

namespace App;

use Decimal\Decimal;

class MoneyTest extends TestCase
{

    public function testAdd()
    {
        $object = new Money(5, Money::EUR);
        $object2 = new Money(4, Money::EUR);
        $objectFromOtherCurrency = new Money(4, Money::JPY);

        $expectedResult = new Money(9, Money::EUR);
        $actualResult = $object->add($object2);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);

        $this->expectExceptionMessage('Cannot add money of different currencies!');
        $object->add($objectFromOtherCurrency);
    }

    public function testMul()
    {
        $object = new Money(5, Money::EUR);
        $multiplier = new Decimal(5);

        $expectedResult = new Money(25, Money::EUR);
        $actualResult = $object->mul($multiplier);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);
    }

    public function testConvert()
    {
        $object = new Money(5, Money::EUR);
        $currency = Money::JPY;

        $expectedResult = new Money('647.65', $currency);
        $actualResult = $object->convert($currency);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);
    }

    public function testDiv()
    {
        $object = new Money(25, Money::EUR);
        $divider = new Decimal(5);

        $expectedResult = new Money(5, Money::EUR);
        $actualResult = $object->div($divider);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);
    }

    public function testIsFromTheSameCurrency()
    {
        $object = new Money(5, Money::EUR);
        $object1 = new Money(5, Money::EUR);
        $object2 = new Money(5, Money::USD);

        $this->assertTrue($object->isFromTheSameCurrency($object1));
        $this->assertFalse($object->isFromTheSameCurrency($object2));
    }

    public function testGt()
    {
        $object = new Money(5, Money::EUR);
        $smallerObject = new Money(3, Money::EUR);
        $biggerObject = new Money(6, Money::EUR);
        $objectFromOtherCurrency = new Money(6, Money::USD);

        $this->assertTrue($object->gt($smallerObject));
        $this->assertFalse($object->gt($biggerObject));

        $this->expectExceptionMessage('Cannot compare money of different currencies!');
        $object->gt($objectFromOtherCurrency);
    }
}
