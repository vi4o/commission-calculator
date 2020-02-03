<?php

namespace App;

use App\Calculator\AbstractCalculatorChain;
use App\Calculator\CashIn;
use App\Calculator\CashOutForLegal;
use App\Calculator\CashOutForNatural;
use App\Input\CsvInput;
use App\Input\InputInterface;
use App\Rounding\CeilToSmallestCurrencyItemRounder;
use App\Rounding\RounderInterface;
use App\Storage\OperationsStorage;
use App\Storage\OperationsStorageInterface;

class CommissionCalculatorIntegrationTest extends TestCase
{
    protected static array $conversionRatesFromEur = [
        'USD' => '1.1497',
        'JPY' => '129.53'
    ];

    protected AbstractCalculatorChain $commissionCalculator;
    protected InputInterface $input;
    protected OperationsStorageInterface $operationsStorage;
    /**
     * @var CeilToSmallestCurrencyItemRounder
     */
    protected RounderInterface $rounder;

    public function setUp(): void
    {
        $currencyConverter = new CurrencyConverter(self::$conversionRatesFromEur);
        $this->operationsStorage = new OperationsStorage($currencyConverter);
        $this->rounder = new CeilToSmallestCurrencyItemRounder();

        $this->commissionCalculator = new CashIn($currencyConverter);
        $this->commissionCalculator
            ->setNext(new CashOutForLegal($currencyConverter))
            ->setNext(new CashOutForNatural($this->operationsStorage, $currencyConverter))
            ->setNext(null);

        $this->input = new CsvInput(__DIR__ . DIRECTORY_SEPARATOR . 'input.csv');
    }

    public function testCommissions()
    {
        $expectedCommissions = [
            '0.60', '3.00', '0.00', '0.06', '0.90', '0', '0.70', '0.30', '0.30', '5.00', '0.00', '0.00', '8612'
        ];

        $actualCommissions = [];
        while ($operation = $this->input->getOperation()) {
            $commission = $this->commissionCalculator->calculateCommission(
                $operation,
                new Money(0, $operation->amount->getCurrency())
            );
            $actualCommissions[] = $this->rounder->round($commission);
            $this->operationsStorage->storeOperation($operation);
        }

        $this->assertEquals($expectedCommissions, $actualCommissions);
    }
}