<?php

namespace App;

use Decimal\Decimal;

class MoneyTest extends TestCase
{
    public function testAdd()
    {
        $object = new Money(5, Currency::EUR);
        $object2 = new Money(4, Currency::EUR);
        $objectFromOtherCurrency = new Money(4, Currency::JPY);

        $expectedResult = new Money(9, Currency::EUR);
        $actualResult = $object->add($object2);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);

        $this->expectExceptionMessage('Cannot add money of different currencies!');
        $object->add($objectFromOtherCurrency);
    }

    public function testSub()
    {
        $object = new Money(5, Currency::EUR);
        $object2 = new Money(4, Currency::EUR);
        $objectFromOtherCurrency = new Money(4, Currency::JPY);

        $expectedResult = new Money(1, Currency::EUR);
        $actualResult = $object->sub($object2);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);

        $this->expectExceptionMessage('Cannot subtract money of different currencies!');
        $object->sub($objectFromOtherCurrency);
    }

    public function testMul()
    {
        $object = new Money(5, Currency::EUR);
        $multiplier = new Decimal(5);

        $expectedResult = new Money(25, Currency::EUR);
        $actualResult = $object->mul($multiplier);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);
    }

    public function testDiv()
    {
        $object = new Money(25, Currency::EUR);
        $divider = new Decimal(5);

        $expectedResult = new Money(5, Currency::EUR);
        $actualResult = $object->div($divider);

        $this->assertMoneyAreEqual($expectedResult, $actualResult);
    }

    public function testIsFromTheSameCurrency()
    {
        $object = new Money(5, Currency::EUR);
        $object1 = new Money(5, Currency::EUR);
        $object2 = new Money(5, Currency::USD);

        $this->assertTrue($object->isFromTheSameCurrency($object1));
        $this->assertFalse($object->isFromTheSameCurrency($object2));
    }

    public function testGt()
    {
        $object = new Money(5, Currency::EUR);
        $smallerObject = new Money(3, Currency::EUR);
        $biggerObject = new Money(6, Currency::EUR);
        $objectFromOtherCurrency = new Money(6, Currency::USD);

        $this->assertTrue($object->gt($smallerObject));
        $this->assertFalse($object->gt($biggerObject));

        $this->expectExceptionMessage('Cannot compare money of different currencies!');
        $object->gt($objectFromOtherCurrency);
    }

    public function testGte()
    {
        $object = new Money(5, Currency::EUR);
        $smallerObject = new Money(3, Currency::EUR);
        $equalObject = new Money(5, Currency::EUR);
        $biggerObject = new Money(6, Currency::EUR);
        $objectFromOtherCurrency = new Money(6, Currency::USD);

        $this->assertTrue($object->gte($smallerObject));
        $this->assertTrue($object->gte($equalObject));
        $this->assertFalse($object->gte($biggerObject));

        $this->expectExceptionMessage('Cannot compare money of different currencies!');
        $object->gte($objectFromOtherCurrency);
    }
}
