<?php


namespace App\Calculator;

use App\Money;
use App\Operation;

interface CalculatorChainLink
{
    public function setNext(?CalculatorChainLink $handler): ?CalculatorChainLink;

    public function calculateCommission(Operation $operation, Money $commissionSoFar): Money;
}
