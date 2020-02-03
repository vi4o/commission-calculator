<?php

namespace App\Input;

use App\TestCase;

class CsvInputTest extends TestCase
{
    protected CsvInput $csvInput;

    public function setUp(): void
    {
        $this->csvInput = new CsvInput(__DIR__ . DIRECTORY_SEPARATOR . 'input.csv');
    }

    public function testGetOperation()
    {
        $expectedOperations = require __DIR__ . DIRECTORY_SEPARATOR . 'operations.php';

        $actualOperations = [];
        while ($operation = $this->csvInput->getOperation()) {
            $actualOperations[] = $operation;
        }

        $this->assertEquals($expectedOperations, $actualOperations);
    }
}
