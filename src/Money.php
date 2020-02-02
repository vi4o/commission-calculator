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
    protected static array $conversionRatesFromEur = [
        2 => '1.1497',
        3 => '129.53'
    ];
    protected Decimal $amount;
    protected int $currencyId;

    /**
     * Currency constructor.
     * @param Decimal|string|int $amount
     * @param string $currency
     * @throws \Exception
     */
    public function __construct($amount, string $currency)
    {
        $this->currencyId = $this->checkCurrencySupported($currency);
        $this->amount = new Decimal($amount);
    }

    /**
     * @param string $currency
     * @return int The currency id of the supported currency
     * @throws \Exception
     */
    protected function checkCurrencySupported(string $currency): int
    {
        if ($id = array_search(strtoupper($currency), self::$currencies)) {
            return $id;
        } else {
            throw new \Exception('Unsupported currency used');
        }
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
        return self::$currencies[$this->currencyId];
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
        return new Money($sum, Money::$currencies[$this->currencyId]);
    }

    /**
     * Multiply by $multiplier
     * @param Decimal $multiplier
     * @return Money
     * @throws \Exception
     */
    public function mul(Decimal $multiplier) : Money
    {
        return new Money($this->amount->mul($multiplier), $this->getCurrency());
    }

    /**
     * Divide by $divider
     * @param Decimal $divider
     * @return Money
     * @throws \Exception
     */
    public function div(Decimal $divider) : Money
    {
        return new Money($this->amount->div($divider), $this->getCurrency());
    }

    public function isFromTheSameCurrency(Money $operand): bool
    {
        return $this->currencyId === $operand->currencyId;
    }

    /**
     * Convert to other currency
     * @param string $currency
     * @return Money
     * @throws \Exception
     */
    public function convert(string $currency) : Money
    {
        $baseCurrencyId = 1;
        $newCurrencyId = $this->checkCurrencySupported($currency);

        $convertedAmount = null;
        if ($newCurrencyId === $this->currencyId) {
            return $this;
        } elseif ($this->currencyId === $baseCurrencyId) {
            $convertedAmount = $this->amount->mul(new Decimal(self::$conversionRatesFromEur[$newCurrencyId]));
        } elseif ($newCurrencyId === $baseCurrencyId) {
            $convertedAmount = $this->amount->div(new Decimal(self::$conversionRatesFromEur[$this->currencyId]));
        } else {
            $convertedAmount = $this->amount
                ->div(new Decimal(self::$conversionRatesFromEur[$this->currencyId]))
                ->mul(new Decimal(self::$conversionRatesFromEur[$newCurrencyId]));
        }

        return new Money($convertedAmount, Money::$currencies[$newCurrencyId]);
    }

    /**
     * Return true if $operand is greater than the object
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
}
