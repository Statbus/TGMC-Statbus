<?php

namespace App\Enum;

enum AdminPermissions: int
{
    case ADMIN = (1 << 0);
    case MENTOR = (1 << 1);
    case BAN = (1 << 2);
    case ASAY = (1 << 3);
    case ADMINTICKET = (1 << 4);
    case FUN = (1 << 5);
    case SERVER = (1 << 6);
    case DEBUG = (1 << 7);
    case PERMISSIONS = (1 << 8);
    case COLOR = (1 << 9);
    case VAREDIT = (1 << 10);
    case SOUND = (1 << 11);
    case SPAWN = (1 << 12);
    case DBRANKS = (1 << 13);
    case RUNTIME = (1 << 14);
    case LOG = (1 << 15);

    public static function getArray(): array
    {
        foreach(self::cases() as $c) {
            $arr[$c->name] = $c->value;
        }
        return $arr;
    }

}
