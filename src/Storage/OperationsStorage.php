<?php

namespace App\Storage;

use App\Currency;
use App\CurrencyConverter;
use App\Money;
use App\Operation;

class OperationsStorage implements OperationsStorageInterface
{
    protected CurrencyConverter $currencyConverter;

    public function __construct(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
        $this->operations = [];
    }

    /**
     * @var Operation[]
     */
    protected array $operations;

    public function storeOperation(Operation $operation) : void
    {
        $this->operations[] = $operation;
    }

    public function getCashOutOperationsValueForLastCalendarWeekForUserInEur(
        int $userId,
        \DateTimeImmutable $now
    ): Money {
        $cashOutOperationsValueForLastCalendarWeek = new Money(0, Currency::EUR);
        foreach ($this->operations as $operation) {
            if ($operation->type === Operation::TYPE_CASH_OUT &&
                $operation->userId === $userId &&
                $this->isInLastCalendarWeekUpToNow($operation->date, $now)
            ) {
                $cashOutOperationsValueForLastCalendarWeek = $cashOutOperationsValueForLastCalendarWeek->add(
                    $this->currencyConverter->convert($operation->amount, Currency::EUR)
                );
            }
        }
        return $cashOutOperationsValueForLastCalendarWeek;
    }

    public function getCashOutOperationsNumberForLastCalendarWeekForUser(int $userId, \DateTimeImmutable $now): int
    {
        $cashOutOperationsNumberForLastCalendarWeek = 0;
        foreach ($this->operations as $operation) {
            if ($operation->type === Operation::TYPE_CASH_OUT &&
                $operation->userId === $userId &&
                $this->isInLastCalendarWeekUpToNow($operation->date, $now)
            ) {
                $cashOutOperationsNumberForLastCalendarWeek++;
            }
        }
        return $cashOutOperationsNumberForLastCalendarWeek;
    }

    protected function isInLastCalendarWeekUpToNow(\DateTimeImmutable $date, \DateTimeImmutable $now) : bool
    {
        $firstDay = new \DateTime();
        $firstDay->setTimestamp(strtotime('monday this week 00:00:00', $now->getTimestamp()));

        return $date >= $firstDay && $date <= $now;
    }
}
