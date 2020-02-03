<?php

namespace App\Storage;

use App\Money;
use App\Operation;

interface OperationsStorageInterface
{
    public function storeOperation(Operation $operation) : void;
    public function getCashOutOperationsValueForLastCalendarWeekForUserInEur(
        int $userId,
        \DateTimeImmutable $now
    ) : Money;
    public function getCashOutOperationsNumberForLastCalendarWeekForUser(int $userId, \DateTimeImmutable $now) : int;
}
