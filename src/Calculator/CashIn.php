<?php

namespace App\Calculator;

use App\Currency;
use App\CurrencyConverter;
use App\Money;
use App\Operation;
use Decimal\Decimal;

class CashIn extends AbstractCalculatorChain
{
    private const COMMISSION_FEE_PERCENTAGE = '0.03';
    private const MAXIMUM_COMMISSION_FEE_EUR = 5;

    private CurrencyConverter $currencyConverter;

    public function __construct(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * @param Operation $operation
     * @param Money $commissionSoFar
     * @return Money
     * @throws \Exception
     */
    public function calculateCommission(Operation $operation, Money $commissionSoFar): Money
    {
        if ($operation->type === Operation::TYPE_CASH_IN) {
            $commission = $operation->amount->mul((new Decimal(self::COMMISSION_FEE_PERCENTAGE))->div(100));

            $maxCommissionFeeInEur = new Money(self::MAXIMUM_COMMISSION_FEE_EUR, Currency::EUR);
            $maxCommissionFeeInOperationCurrency = $this->currencyConverter->convert(
                $maxCommissionFeeInEur,
                $operation->amount->getCurrency()
            );
            $commission = $commission->gt($maxCommissionFeeInOperationCurrency) ?
                $maxCommissionFeeInOperationCurrency :
                $commission;

            return parent::calculateCommission($operation, $commissionSoFar->add($commission));
        } else {
            return parent::calculateCommission($operation, $commissionSoFar);
        }

    }
}
