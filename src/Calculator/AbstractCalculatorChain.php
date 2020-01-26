<?php

namespace App\Calculator;

use App\Money;
use App\Operation;

abstract class AbstractCalculatorChain implements CalculatorChainLink
{
    private ?CalculatorChainLink $nextCalculatorChainLink;

    public function setNext(?CalculatorChainLink $calculatorChainLink): ?CalculatorChainLink
    {
        $this->nextCalculatorChainLink = $calculatorChainLink;
        return $calculatorChainLink;
    }

    public function calculateCommission(Operation $operation, Money $commissionSoFar): Money
    {
        if ($this->nextCalculatorChainLink) {
            return $this->nextCalculatorChainLink->calculateCommission($operation, $commissionSoFar);
        }

        return $commissionSoFar;
    }
}
