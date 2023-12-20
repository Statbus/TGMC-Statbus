<?php

namespace App\Domain\Stat\Repository;

use App\Repository\Repository;
use App\Domain\Stat\Entity\Stat;

class StatRepository extends Repository
{
    public ?string $entityClass = Stat::class;

    public function getStatsForRound(int $round): array
    {
        $query = $this->directQuery(
            table: 'feedback f',
            columns:[
                'f.id',
                'f.round_id as `round`',
                'f.key_name as `name`',
                'f.version',
                'f.key_type as `type`',
                'f.json',
            ],
            where:[
                'f.round_id = ?'
            ],
            order: [
                'f.key_name ASC'
            ]
        );
        $this->run($query, [$round]);
        return $this->getResults();
    }

    public function getStat(int $round, string $name): Stat
    {
        $query = $this->directQuery(
            table: 'feedback f',
            columns:[
                'f.id',
                'f.round_id as `round`',
                'f.key_name as `name`',
                'f.version',
                'f.key_type as `type`',
                'f.json'
            ],
            where:[
                'f.round_id = ?',
                'f.key_name = ?'
            ],
            order: []
        );
        $this->row($query, [$round, $name]);
        return $this->getResult();
    }

}
