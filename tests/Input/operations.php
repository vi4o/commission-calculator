<?php

use App\Money;
use App\Operation;

$operations = [];

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2014-12-31');
$operation->userId = 4;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(1200, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2015-01-01');
$operation->userId = 4;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(1000, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-05');
$operation->userId = 4;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(1000, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-05');
$operation->userId = 1;
$operation->userType = 'natural';
$operation->type = 'cash_in';
$operation->amount = new Money(200, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-06');
$operation->userId = 2;
$operation->userType = 'legal';
$operation->type = 'cash_out';
$operation->amount = new Money(300, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-06');
$operation->userId = 1;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(30000, 'JPY');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-07');
$operation->userId = 1;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(1000, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-07');
$operation->userId = 1;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(100, 'USD');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-10');
$operation->userId = 1;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(100, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-10');
$operation->userId = 2;
$operation->userType = 'legal';
$operation->type = 'cash_in';
$operation->amount = new Money(1000000, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-01-10');
$operation->userId = 3;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(1000, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-02-15');
$operation->userId = 1;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(300, 'EUR');
$operations[] = $operation;

$operation = new Operation();
$operation->date = new \DateTimeImmutable('2016-02-19');
$operation->userId = 5;
$operation->userType = 'natural';
$operation->type = 'cash_out';
$operation->amount = new Money(3000000, 'JPY');
$operations[] = $operation;

return $operations;
