<?php

namespace App\Storage;

use App\Currency;
use App\CurrencyConverter;
use App\Money;
use App\Operation;
use App\TestCase;

class OperationsStorageTest extends TestCase
{

    protected static array $conversionRatesFromEur = [
        'USD' => '1.1497',
        'JPY' => '129.53'
    ];

    private OperationsStorage $object;

    public function setUp(): void
    {
        $this->object = new OperationsStorage(new CurrencyConverter(self::$conversionRatesFromEur));
    }

    public function testGetCashOutOperationsNumberAndValueForLastCalendarWeekForUser()
    {
        $operations = [];

        //Not in the last week
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-02');
        $operation->userId = 1;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->amount = new Money(10, Currency::EUR);
        $operations[] = $operation;

        //Cash in
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->userId = 1;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->type = Operation::TYPE_CASH_IN;
        $operation->amount = new Money(10, Currency::EUR);
        $operations[] = $operation;

        //Another user
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->userId = 2;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->amount = new Money(10, Currency::EUR);
        $operations[] = $operation;

        //valid to sum
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->userId = 1;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->amount = new Money(10, Currency::EUR);
        $operations[] = $operation;

        //valid to sum
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-04');
        $operation->userId = 1;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->amount = new Money(10, Currency::EUR);
        $operations[] = $operation;

        //After now -> must not happen
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-09');
        $operation->userId = 1;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->amount = new Money(10, Currency::EUR);
        $operations[] = $operation;

        foreach ($operations as $operation) {
            $this->object->storeOperation($operation);
        }

        $expectedOperationsNumber = 2;
        $actualOperationsNumber = $this->object->getCashOutOperationsNumberForLastCalendarWeekForUser(
            1,
            new \DateTimeImmutable('2020-02-08')
        );

        $this->assertEquals($expectedOperationsNumber, $actualOperationsNumber);

        $expectedOperationsValue = new Money(20, Currency::EUR);
        $actualOperationsValue = $this->object->getCashOutOperationsValueForLastCalendarWeekForUserInEur(
            1,
            new \DateTimeImmutable('2020-02-08')
        );

        $this->assertMoneyAreEqual($expectedOperationsValue, $actualOperationsValue);
    }
}
