<?php

require_once 'vendor/autoload.php';

use App\Calculator\CashIn;
use App\Calculator\CashOutForLegal;
use App\Calculator\CashOutForNatural;
use App\CurrencyConverter;
use App\Input\CsvInput;
use App\Money;
use App\Rounding\CeilToSmallestCurrencyItemRounder;
use App\Storage\OperationsStorage;

$conversionRatesFromEur = [
    'USD' => '1.1497',
    'JPY' => '129.53'
];
$currencyConverter = new CurrencyConverter($conversionRatesFromEur);
$operationsStorage = new OperationsStorage($currencyConverter);
$rounder = new CeilToSmallestCurrencyItemRounder();

$commissionCalculator = new CashIn($currencyConverter);
$commissionCalculator
    ->setNext(new CashOutForLegal($currencyConverter))
    ->setNext(new CashOutForNatural($operationsStorage, $currencyConverter))
    ->setNext(null);

if ($argc == 2) {
    $input = new CsvInput($argv[1]);

    while ($operation = $input->getOperation()) {
        $commission = $commissionCalculator->calculateCommission(
            $operation,
            new Money(0, $operation->amount->getCurrency())
        );
        echo $rounder->round($commission) . PHP_EOL;
        $operationsStorage->storeOperation($operation);
    }
} else {
    echo "Use the following format 'php commission-calculator.php input.csv'";
}
