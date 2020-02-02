<?php

namespace App\Calculator;

use App\Money;
use App\Operation;
use App\TestCase;

class CashInTest extends TestCase
{
    /**
     * @var CashIn
     */
    private CashIn $commissionCalculator;

    public function setUp(): void
    {
        $this->commissionCalculator = new CashIn();
        $this->commissionCalculator->setNext(null);
    }

    public function testCommissionIsCalculatedCorrectlyForBothUserTypes()
    {
        $operationForLegal = new Operation();
        $operationForLegal->date = new \DateTimeImmutable();
        $operationForLegal->amount = new Money(10000, Money::EUR);
        $operationForLegal->type = Operation::TYPE_CASH_IN;
        $operationForLegal->userType = Operation::USER_TYPE_LEGAL;

        $operationForNatural = new Operation();
        $operationForNatural->date = new \DateTimeImmutable();
        $operationForNatural->amount = new Money(10000, Money::EUR);
        $operationForNatural->type = Operation::TYPE_CASH_IN;
        $operationForNatural->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Money::EUR);

        $commissionForLegal = $this->commissionCalculator->calculateCommission($operationForLegal, $initialCommission);
        $commissionForNatural = $this->commissionCalculator->calculateCommission($operationForNatural, $initialCommission);

        $expectedCommission = new Money(3, Money::EUR);

        $this->assertMoneyAreEqual($expectedCommission, $commissionForLegal);
        $this->assertMoneyAreEqual($expectedCommission, $commissionForNatural);
    }

    public function testThatIfCommissionExceedsMaximumItIsSetToIt()
    {
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable();
        $operation->amount = new Money(100000, Money::EUR);
        $operation->type = Operation::TYPE_CASH_IN;
        $operation->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Money::EUR);

        $commission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $expectedCommission = new Money(5, Money::EUR);

        $this->assertMoneyAreEqual($expectedCommission, $commission);
    }

    public function testForCommissionInOtherCurrencyThanEur()
    {
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable();
        $operation->amount = new Money(10000, Money::JPY);
        $operation->type = Operation::TYPE_CASH_IN;
        $operation->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Money::JPY);

        $commission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $expectedCommission = new Money(3, Money::JPY);

        $this->assertMoneyAreEqual($expectedCommission, $commission);
    }

    public function testThatIfACommissionInOtherCurrencyExceedsMaximumItIsSetToIt()
    {
        $operation = new Operation();
        $operation->date = new \DateTimeImmutable();
        $operation->amount = new Money(10000000, Money::JPY);
        $operation->type = Operation::TYPE_CASH_IN;
        $operation->userType = Operation::USER_TYPE_NATURAL;

        $initialCommission = new Money(0, Money::JPY);

        $commission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $expectedCommission = new Money('647.65', Money::JPY); //the amount of 5 EUR in JPY

        $this->assertMoneyAreEqual($expectedCommission, $commission);
    }
}
