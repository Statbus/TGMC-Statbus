<?php

namespace App\Domain\Ban\Enum;

enum Status: string
{
    case EXPIRED = 'Expired';
    case LIFTED = 'Lifted';
    case ACTIVE = 'Active';
    case PERMANENT = 'Permanent';

    public function getCssClass(): string
    {
        return match($this) {
            Status::EXPIRED => 'success',
            Status::LIFTED => 'info',
            Status::ACTIVE => 'danger',
            Status::PERMANENT => 'perma'
        };
    }

    public function getArticle(): string
    {
        return match($this) {
            Status::EXPIRED => 'an',
            Status::LIFTED => 'a',
            Status::ACTIVE => 'an',
            Status::PERMANENT => 'a'
        };
    }

}
