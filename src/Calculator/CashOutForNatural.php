<?php

namespace App\Calculator;

use App\Currency;
use App\CurrencyConverter;
use App\Money;
use App\Operation;
use App\Storage\OperationsStorageInterface;
use Decimal\Decimal;

class CashOutForNatural extends AbstractCalculatorChain
{
    private const DEFAULT_COMMISSION_FEE_PERCENT = '0.3';
    private const NUMBER_OF_OPERATIONS_ELIGIBLE_FOR_DISCOUNT = 3;
    private const AMOUNT_UP_TO_WHICH_DISCOUNT_IS_APPLIED_IN_EUR = 1000;

    protected OperationsStorageInterface $operationsStorage;
    protected CurrencyConverter $currencyConverter;

    public function __construct(OperationsStorageInterface $operationsStorage, CurrencyConverter $currencyConverter)
    {
        $this->operationsStorage = $operationsStorage;
        $this->currencyConverter = $currencyConverter;
    }

    public function calculateCommission(Operation $operation, Money $commissionSoFar): Money
    {
        if ($operation->type !== Operation::TYPE_CASH_OUT || $operation->userType !== Operation::USER_TYPE_NATURAL) {
            return parent::calculateCommission($operation, $commissionSoFar);
        }

        $numberOfCashOutOperationsForTheCurrentWeekUpToNow = $this->operationsStorage
            ->getCashOutOperationsNumberForLastCalendarWeekForUser($operation->userId, $operation->date);
        $amountForTheCurrentWeekUpToNowInEur = $this->operationsStorage
            ->getCashOutOperationsValueForLastCalendarWeekForUserInEur($operation->userId, $operation->date);
        $amountUpToWhichDiscountIsAppliedInEur = new Money(
            self::AMOUNT_UP_TO_WHICH_DISCOUNT_IS_APPLIED_IN_EUR,
            Currency::EUR
        );


        //Check if discount should be not be applied
        if ($numberOfCashOutOperationsForTheCurrentWeekUpToNow >= self::NUMBER_OF_OPERATIONS_ELIGIBLE_FOR_DISCOUNT ||
            $amountForTheCurrentWeekUpToNowInEur->gte($amountUpToWhichDiscountIsAppliedInEur)
        ) {
            $commission = $operation->amount
                ->mul((new Decimal(self::DEFAULT_COMMISSION_FEE_PERCENT))->div(100));
        } else {
            $amountLeftForDiscount = $amountUpToWhichDiscountIsAppliedInEur->sub($amountForTheCurrentWeekUpToNowInEur);
            $amountOfTheOperationInEur = $this->currencyConverter->convert($operation->amount, Currency::EUR);

            if ($amountOfTheOperationInEur->gt($amountLeftForDiscount)) {
                $commission = $amountOfTheOperationInEur->sub($amountLeftForDiscount)
                    ->mul((new Decimal(self::DEFAULT_COMMISSION_FEE_PERCENT))->div(100));
                $commission = $this->currencyConverter->convert($commission, $operation->amount->getCurrency());
            } else {
                $commission = new Money(0, $operation->amount->getCurrency());
            }
        }

        return parent::calculateCommission($operation, $commissionSoFar->add($commission));
    }
}
