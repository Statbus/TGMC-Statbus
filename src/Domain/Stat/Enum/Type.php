<?php

namespace App\Domain\Stat\Enum;

enum Type: string
{
    case AMOUNT = 'amount';
    case ASSOCIATIVE = 'associative';
    case TALLY = 'tally';
    case TEXT = 'text';
}
