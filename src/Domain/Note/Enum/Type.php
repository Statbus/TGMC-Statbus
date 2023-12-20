<?php

namespace App\Domain\Note\Enum;

enum Type: string
{
    case MEMO = 'memo';
    case MESSAGE = 'message';
    case MESSAGE_SENT = 'message sent';
    case NOTE = 'note';
    case WATCHLIST = 'watchlist entry';

    public function getIcon(): string
    {
        return match($this) {
            Type::MEMO => 'fa-solid fa-scroll',
            Type::MESSAGE => 'fa-solid fa-message',
            Type::MESSAGE_SENT => 'fa-regular fa-message',
            Type::NOTE => 'fa-solid fa-note-sticky',
            Type::WATCHLIST => 'fa-solid fa-binoculars'
        };
    }
}
