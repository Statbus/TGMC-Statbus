<?php

namespace App\Exception;

use Exception;

class StatbusNotYourTicketException extends Exception
{
    protected $message = "You do not have permission to view this ticket.";
}
