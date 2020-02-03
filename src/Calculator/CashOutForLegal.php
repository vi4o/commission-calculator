<?php

namespace App\Calculator;

use App\Currency;
use App\CurrencyConverter;
use App\Money;
use App\Operation;
use Decimal\Decimal;

class CashOutForLegal extends AbstractCalculatorChain
{
    private const DEFAULT_COMMISSION_FEE_PERCENT = '0.3';
    private const MINIMUM_COMMISSION_FEE_IN_EUR = '0.5';

    private CurrencyConverter $currencyConverter;

    public function __construct(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    public function calculateCommission(Operation $operation, Money $commissionSoFar): Money
    {
        if ($operation->type !== Operation::TYPE_CASH_OUT || $operation->userType !== Operation::USER_TYPE_LEGAL) {
            return parent::calculateCommission($operation, $commissionSoFar);
        }

        $minimumCommissionFee = new Money(self::MINIMUM_COMMISSION_FEE_IN_EUR, Currency::EUR);
        $minimumCommissionFeeInOpCurrency = $this->currencyConverter
            ->convert($minimumCommissionFee, $operation->amount->getCurrency());

        $commission = $operation->amount
            ->mul((new Decimal(self::DEFAULT_COMMISSION_FEE_PERCENT))->div(100));

        if ($minimumCommissionFeeInOpCurrency->gt($commission)) {
            $commission = $minimumCommissionFeeInOpCurrency;
        }

        return parent::calculateCommission($operation, $commissionSoFar->add($commission));
    }
}