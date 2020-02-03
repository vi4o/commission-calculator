<?php

namespace App;

use Decimal\Decimal;

class Money
{
    protected Decimal $amount;
    protected string $currency;

    /**
     * Currency constructor.
     * @param Decimal|string|int $amount
     * @param string $currency
     * @throws \Exception
     */
    public function __construct($amount, string $currency)
    {
        $this->currency = Currency::checkCurrencySupported($currency);
        $this->amount = new Decimal($amount);
    }

    /**
     * @return Decimal
     */
    public function getAmount() : Decimal
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency() : string
    {
        return $this->currency;
    }

    /**
     * Add $operand money
     * @param Money $operand
     * @return Money
     * @throws \Exception
     */
    public function add(Money $operand): Money
    {
        if ($this->isFromTheSameCurrency($operand)) {
            $sum = $this->amount->add($operand->getAmount());
        } else {
            throw new \Exception('Cannot add money of different currencies!');
        }
        return new Money($sum, $this->currency);
    }

    /**
     * Subtract $operand money
     * @param Money $operand
     * @return Money
     * @throws \Exception
     */
    public function sub(Money $operand): Money
    {
        if ($this->isFromTheSameCurrency($operand)) {
            $sum = $this->amount->sub($operand->getAmount());
        } else {
            throw new \Exception('Cannot subtract money of different currencies!');
        }
        return new Money($sum, $this->currency);
    }

    /**
     * Multiply by $multiplier
     * @param Decimal $multiplier
     * @return Money
     * @throws \Exception
     */
    public function mul(Decimal $multiplier) : Money
    {
        return new Money($this->amount->mul($multiplier), $this->currency);
    }

    /**
     * Divide by $divider
     * @param Decimal $divider
     * @return Money
     * @throws \Exception
     */
    public function div(Decimal $divider) : Money
    {
        return new Money($this->amount->div($divider), $this->currency);
    }

    public function isFromTheSameCurrency(Money $operand): bool
    {
        return $this->currency === $operand->currency;
    }

    /**
     * Return true if $this is greater than the $operand
     * @param Money $operand
     * @return bool
     * @throws \Exception
     */
    public function gt(Money $operand) : bool
    {
        if ($this->isFromTheSameCurrency($operand)) {
            return $this->amount->compareTo($operand->getAmount()) === 1;
        } else {
            throw new \Exception('Cannot compare money of different currencies!');
        }
    }

    public function __toString()
    {
        return $this->amount->toFixed(2) . ' ' . $this->getCurrency();
    }

    /**
     * Return true if $this is greater than or equal to the $operand
     * @param Money $operand
     * @return bool
     * @throws \Exception
     */
    public function gte(Money $operand) : bool
    {
        if ($this->isFromTheSameCurrency($operand)) {
            $comparisonResult = $this->amount->compareTo($operand->getAmount());
            return $comparisonResult === 1 || $comparisonResult === 0;
        } else {
            throw new \Exception('Cannot compare money of different currencies!');
        }
    }
}
