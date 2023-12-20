<?php

namespace App\Domain\Player\Data;

enum Standing: string
{
    case NOT_BANNED = 'No active bans';
    case ACTIVE_BANS = 'Active bans';
    case PERMABANNED = 'Permabanned';

    public function getCssClass(): string
    {
        return match($this) {
            Standing::NOT_BANNED => 'success text-white',
            Standing::ACTIVE_BANS => 'danger',
            Standing::PERMABANNED => 'perma'
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            Standing::NOT_BANNED => 'circle-check',
            Standing::ACTIVE_BANS => 'ban',
            Standing::PERMABANNED => 'bolt-lightning'
        };
    }


}
