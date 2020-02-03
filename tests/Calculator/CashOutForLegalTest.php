<?php

namespace App\Calculator;

use App\Currency;
use App\CurrencyConverter;
use App\Money;
use App\Operation;
use App\TestCase;

class CashOutForLegalTest extends TestCase
{
    protected static array $conversionRatesFromEur = [
        'USD' => '2',
        'JPY' => '100'
    ];

    protected CashOutForLegal $commissionCalculator;

    public function setUp(): void
    {
        $this->commissionCalculator = new CashOutForLegal(new CurrencyConverter(self::$conversionRatesFromEur));
        $this->commissionCalculator->setNext(null);
    }

    public function testCalculateCommission()
    {
        $expectedCommission = new Money('0.6', Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(200, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_LEGAL;
        $operation->userId = 1;

        $commissionSoFar = new Money(0, Currency::EUR);

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $commissionSoFar);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }

    public function testCalculateCommissionWhenUnderMinimumCommission()
    {
        $expectedCommission = new Money('0.5', Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(50, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_LEGAL;
        $operation->userId = 1;

        $commissionSoFar = new Money(0, Currency::EUR);

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $commissionSoFar);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }

    public function testCalculateCommissionForNaturalShouldBeTheCommissionSoFar()
    {
        $expectedCommission = new Money(50, Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(200, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->userId = 1;

        $commissionSoFar = $expectedCommission;

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $commissionSoFar);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }

    public function testCalculateCommissionForCashInShouldReturnTheCommissionSoFar()
    {
        $expectedCommission = new Money(50, Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(200, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_IN;
        $operation->userType = Operation::USER_TYPE_LEGAL;
        $operation->userId = 1;

        $commissionSoFar = $expectedCommission;

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $commissionSoFar);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }
}
