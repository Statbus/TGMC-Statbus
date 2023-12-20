<?php

namespace App\Exception;

use Exception;

class StatbusUnauthorizedException extends Exception
{
    protected $message = "You do not have permission to view this page";
}
