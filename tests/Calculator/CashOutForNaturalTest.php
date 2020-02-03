<?php

namespace App\Calculator;

use App\Currency;
use App\CurrencyConverter;
use App\Money;
use App\Operation;
use App\Storage\OperationsStorage;
use App\TestCase;

class CashOutForNaturalTest extends TestCase
{
    protected static array $conversionRatesFromEur = [
        'USD' => '2',
        'JPY' => '100'
    ];

    private CashOutForNatural $commissionCalculator;
    /**
     * @var OperationsStorage|\PHPUnit\Framework\MockObject\MockObject
     */
    private $operationStorageMock;

    public function setUp(): void
    {
        $this->operationStorageMock = $this->getMockBuilder(OperationsStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commissionCalculator = new CashOutForNatural(
            $this->operationStorageMock,
            new CurrencyConverter(self::$conversionRatesFromEur)
        );
        $this->commissionCalculator->setNext(null);
    }

    public function testCalculateCommissionWhenDiscountIsFullyApplied()
    {
        $expectedCommission = new Money(0, Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(500, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->userId = 1;

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsNumberForLastCalendarWeekForUser')
            ->with($operation->userId, $operation->date)
            ->willReturn(2);

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsValueForLastCalendarWeekForUserInEur')
            ->with($operation->userId, $operation->date)
            ->willReturn(new Money(0, Currency::EUR));

        $initialCommission = new Money(0, Currency::EUR);

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }

    public function testCalculateCommissionWhenDiscountIsPartiallyApplied()
    {
        $expectedCommission = new Money(3, Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(2000, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->userId = 1;

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsNumberForLastCalendarWeekForUser')
            ->with($operation->userId, $operation->date)
            ->willReturn(2);

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsValueForLastCalendarWeekForUserInEur')
            ->with($operation->userId, $operation->date)
            ->willReturn(new Money(0, Currency::EUR));

        $initialCommission = new Money(0, Currency::EUR);

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }

    public function testCalculateCommissionWhenDiscountIsPartiallyApplied2()
    {
        $expectedCommission = new Money('4.5', Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(2000, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->userId = 1;

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsNumberForLastCalendarWeekForUser')
            ->with($operation->userId, $operation->date)
            ->willReturn(2);

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsValueForLastCalendarWeekForUserInEur')
            ->with($operation->userId, $operation->date)
            ->willReturn(new Money(500, Currency::EUR));

        $initialCommission = new Money(0, Currency::EUR);

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }

    public function testCalculateCommissionWhenDiscountIsNotAppliedDueToOperationCountBiggerThanLimit()
    {
        $expectedCommission = new Money(3, Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(1000, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->userId = 1;

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsNumberForLastCalendarWeekForUser')
            ->with($operation->userId, $operation->date)
            ->willReturn(3);

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsValueForLastCalendarWeekForUserInEur')
            ->with($operation->userId, $operation->date)
            ->willReturn(new Money(100, Currency::EUR));

        $initialCommission = new Money(0, Currency::EUR);

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }

    public function testCalculateCommissionWhenDiscountIsNotAppliedDueToAmountBiggerThanLimit()
    {
        $expectedCommission = new Money(3, Currency::EUR);

        $operation = new Operation();
        $operation->date = new \DateTimeImmutable('2020-02-03');
        $operation->amount = new Money(1000, Currency::EUR);
        $operation->type = Operation::TYPE_CASH_OUT;
        $operation->userType = Operation::USER_TYPE_NATURAL;
        $operation->userId = 1;

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsNumberForLastCalendarWeekForUser')
            ->with($operation->userId, $operation->date)
            ->willReturn(3);

        $this->operationStorageMock->expects($this->any())
            ->method('getCashOutOperationsValueForLastCalendarWeekForUserInEur')
            ->with($operation->userId, $operation->date)
            ->willReturn(new Money(1000, Currency::EUR));

        $initialCommission = new Money(0, Currency::EUR);

        $actualCommission = $this->commissionCalculator->calculateCommission($operation, $initialCommission);

        $this->assertMoneyAreEqual($expectedCommission, $actualCommission);
    }

}
