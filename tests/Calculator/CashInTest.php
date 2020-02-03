<?php

namespace App\Calculator;

use App\Currency;
use App\CurrencyConverter;
use App\Money;
use App\Operation;
use App\TestCase;

class CashInTest extends TestCase
{
    /**
     * @var CashIn
     */
    private CashIn $commissionCalculator;

    protected static array $conversionRatesFromEur = [
        'USD' => '2',
        'JPY' => '100'
    ];

    public function setUp(): void
    {
        $this->commissionCalculator = new CashIn(new CurrencyConverter(self::$conversionRatesFromEur));
        $this->commissionCalculator->setNext(null);
    }

    public function testCommissionIsCalculatedCorrectlyForBothUserTypes()
    {
        $operationForLegal = new Operation();
        $operationForLegal->date = new \DateTimeImmutable();
        $operationForLegal->amount = new Money(10000, Currency::EUR);
        $operationForLegal->type = Operation::TYPE_CASH_IN;
        $operationForLegal->userType = Operation::USER_TYPE_LEGAL;

        $operationForNatural = new Operation();
        $operationForNatural->date = new \DateTimeImmutable();
        $operationForNatural->amount = new Money(10000, Currency::EUR);
        $operationForNatural->type = Operation::TYPE_CASH_IN;
        $operationForNatural->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Currency::EUR);

        $commissionForLegal = $this->commissionCalculator->calculateCommission($operationForLegal, $initialCommission);
        $commissionForNatural = $this->commissionCalculator->calculateCommission($operationForNatural, $initialCommission);

        $expectedCommission = new Money(3, Currency::EUR);

        $this->assertMoneyAreEqual($expectedCommission, $commissionForLegal);
        $this->assertMoneyAreEqual($expectedCommission, $commissionForNatural);
    }

    public function testThatIfCommissionExceedsMaximumItIsSetToIt()
    {
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable();
        $operation->amount = new Money(100000, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_IN;
        $operation->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Currency::EUR);

        $commission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $expectedCommission = new Money(5, Currency::EUR);

        $this->assertMoneyAreEqual($expectedCommission, $commission);
    }

    public function testForCommissionInOtherCurrencyThanEur()
    {
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable();
        $operation->amount = new Money(10000, Currency::JPY);
        $operation->type = Operation::TYPE_CASH_IN;
        $operation->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Currency::JPY);

        $commission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $expectedCommission = new Money(3, Currency::JPY);

        $this->assertMoneyAreEqual($expectedCommission, $commission);
    }

    public function testThatIfACommissionInOtherCurrencyExceedsMaximumItIsSetToIt()
    {
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable();
        $operation->amount = new Money(10000000, Currency::JPY);
        $operation->type = Operation::TYPE_CASH_IN;
        $operation->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Currency::JPY);

        $commission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $expectedCommission = new Money('500', Currency::JPY); //the amount of 5 EUR in JPY

        $this->assertMoneyAreEqual($expectedCommission, $commission);
    }

    public function testThatIfOperationIsOtherThanCashInNoCommissionIsCalculated()
    {
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable();
        $operation->amount = new Money(10000, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Currency::EUR);

        $commission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $expectedCommission = new Money(0, Currency::EUR);

        $this->assertMoneyAreEqual($expectedCommission, $commission);
    }
}
