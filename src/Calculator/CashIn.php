<?php

namespace App\Calculator;

use App\Money;
use App\Operation;
use Decimal\Decimal;

class CashIn extends AbstractCalculatorChain
{
    private const COMMISSION_FEE_PERCENTAGE = '0.03';
    private const MAXIMUM_COMMISSION_FEE_EUR = 5;

    /**
     * @param Operation $operation
     * @param Money $commissionSoFar
     * @return Money
     * @throws \Exception
     */
    public function calculateCommission(Operation $operation, Money $commissionSoFar): Money
    {
        $commission = $operation->amount->mul((new Decimal(self::COMMISSION_FEE_PERCENTAGE))->mul('0.01'));

        $maxCommissionFeeInEur = new Money(self::MAXIMUM_COMMISSION_FEE_EUR, Money::EUR);
        $maxCommissionFeeInOperationCurrency = $maxCommissionFeeInEur->convert($operation->amount->getCurrency());
        $commission = $commission->gt($maxCommissionFeeInOperationCurrency) ?
            $maxCommissionFeeInOperationCurrency :
            $commission;

        return parent::calculateCommission($operation, $commissionSoFar->add($commission));
    }
}
