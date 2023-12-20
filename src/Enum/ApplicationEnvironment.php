<?php

namespace App\Enum;

enum ApplicationEnvironment: string
{
    case LOCAL = 'local';
    case DEV = 'dev';
    case TEST = 'test';
    case PROD = 'prod';

    public function enableDebug(): bool
    {
        return match($this) {
            ApplicationEnvironment::DEV, ApplicationEnvironment::LOCAL => true,
            default => false
        };
    }

}
