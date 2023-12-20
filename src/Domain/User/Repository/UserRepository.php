<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use App\Repository\Repository;

class UserRepository extends Repository
{
    public ?string $entityClass = User::class;

    public function getUserByCkey(string $ckey): User
    {
        return $this->row("SELECT
        p.ckey,
        p.byond_key as byondKey,
        p.ip,
        SUBSTRING_INDEX(SUBSTRING_INDEX(a.rank, '+', 1), ',', -1) as rank, 
        (SELECT r.flags FROM admin_ranks r WHERE rank = SUBSTRING_INDEX(SUBSTRING_INDEX(a.rank, '+', 1), ',', -1)) as flags,
        p.lastseen as lastSeen
        FROM player p 
        LEFT JOIN `admin` a ON a.ckey = p.ckey 
        LEFT JOIN admin_ranks r ON r.rank = a.rank 
        WHERE p.ckey = ?", [$ckey])->getResult();
    }

}
