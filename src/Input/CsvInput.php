<?php

namespace App\Input;

use App\Money;
use App\Operation;

class CsvInput implements InputInterface
{
    protected $csvFile;
    /**
     * @var string
     */
    private string $csvFilename;

    public function __construct(string $csvFilename)
    {
        $this->csvFile = fopen($csvFilename, 'r');
        $this->csvFilename = $csvFilename;
    }

    public function getOperation(): ?Operation
    {
        $operation = fgetcsv($this->csvFile);
        switch ($operation) {
            case false:
                return null;
            case null:
                throw new \Exception('Cannot read from file ' . $this->csvFilename);
            default:
                $operationObject = new Operation();
                $operationObject->date = new \DateTimeImmutable($operation[0]);
                $operationObject->userId = intval($operation[1]);
                $operationObject->userType = $operation[2];
                $operationObject->type = $operation[3];
                $operationObject->amount = new Money($operation[4], $operation[5]);

                return $operationObject;
        }
    }
}