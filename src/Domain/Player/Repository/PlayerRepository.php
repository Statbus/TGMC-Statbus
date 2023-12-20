<?php

namespace App\Domain\Player\Repository;

use App\Domain\Player\Entity\Player;
use App\Repository\Repository;

class PlayerRepository extends Repository
{
    public ?string $entityClass = Player::class;

    public string $table = 'player p';

    public array $columns = [
        'p.ckey',
        'p.byond_key',
        'p.firstseen as `firstSeen`',
        'p.firstseen_round_id as `firstRound`',
        'p.lastseen as `lastSeen`',
        'p.lastseen_round_id as `lastRound`',
        'p.ip',
        'p.computerid as `cid`',
        'p.accountjoindate as `accountJoinDate`',
        'a.rank',
        'r.flags'
    ];

    public array $joins = [
        'LEFT JOIN `admin` a ON a.ckey = p.ckey',
        'LEFT JOIN admin_ranks r ON r.rank = a.rank'
    ];

    public function getPlayer(string $ckey): Player
    {
        $this->where = [
            'p.ckey = ?'
        ];
        $query = $this->buildQuery();
        return $this->row($query, [$ckey])->getResult();
    }

}
