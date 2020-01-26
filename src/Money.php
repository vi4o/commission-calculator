<?php

namespace App;

use Decimal\Decimal;

class Money
{
    public const EUR = 'EUR';
    public const USD = 'USD';
    public const JPY = 'JPY';
    protected static array $currencies = [
        1 => self::EUR,
        2 => self::USD,
        3 => self::JPY,
    ];
    public Decimal $amount;
    public int $currencyId;

    /**
     * Currency constructor.
     * @param Decimal|string|int $amount
     * @param string $currency
     * @throws \Exception
     */
    public function __construct($amount, string $currency)
    {
        if ($id = array_search(strtoupper($currency), self::$currencies)) {
            $this->currencyId = $id;
        } else {
            throw new \Exception('Unsupported currency used');
        }
        $this->amount = new Decimal($amount);
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
            $sum = $this->amount->add($operand->amount);
        } else {
            throw new \Exception('Cannot add money of different currencies');
        }
        return new Money($sum, Money::$currencies[$this->currencyId]);
    }

    public function isFromTheSameCurrency(Money $operand): bool
    {
        return $this->currencyId === $operand->currencyId;
    }
}
