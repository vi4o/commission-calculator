<?php

namespace App\Calculator;


use App\Money;
use App\Operation;
use PHPUnit\Framework\TestCase;

class AbstractCalculatorChainTest extends TestCase
{
    private AbstractCalculatorChain $calculator;

    public function setUp(): void
    {
        $first = new class() extends AbstractCalculatorChain {
            public function calculateCommission(Operation $operation, Money $commissionSoFar): Money
            {
                $someCommission = new Money('5.55', 'EUR');
                $newCommission = $commissionSoFar->add($someCommission);
                return parent::calculateCommission($operation, $newCommission);
            }
        };

        $second = new class() extends AbstractCalculatorChain {
            public function calculateCommission(Operation $operation, Money $commissionSoFar): Money
            {
                $someCommission = new Money('-1.51', 'EUR');
                $newCommission = $commissionSoFar->add($someCommission);
                return parent::calculateCommission($operation, $newCommission);
            }
        };

        $first->setNext($second);
        $second->setNext(null);

        $this->calculator = $first;
    }

    public function testCalculateCommission()
    {
        $expectedCommission = new Money('4.04', 'EUR');
        $initialCommission = new Money('0', 'EUR');

        $operationMock = $this->createMock(Operation::class);
        $actualCommission = $this->calculator->calculateCommission($operationMock, $initialCommission);

        $this->assertEquals($expectedCommission->amount->toFixed(2), $actualCommission->amount->toFixed(2));
        $this->assertEquals($expectedCommission->currencyId, $actualCommission->currencyId);

    }
}
