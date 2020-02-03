<?php

namespace App\Input;

use App\Operation;

interface InputInterface
{
    public function getOperation() : ?Operation;
}