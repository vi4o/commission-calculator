<?php

namespace App;

class Operation
{
    public const USER_TYPE_NATURAL = 'natural';
    public const USER_TYPE_LEGAL = 'legal';

    public const TYPE_CASH_IN = 'cash_in';
    public const TYPE_CASH_OUT = 'cash_out';

    public \DateTimeImmutable $date;
    public int $userId;
    public string $userType;
    public string $type;
    public Money $amount;
}
