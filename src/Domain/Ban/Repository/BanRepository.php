<?php

namespace App\Domain\Ban\Repository;

use App\Domain\Ban\Entity\Ban;
use App\Repository\Repository;

class BanRepository extends Repository
{
    public ?string $entityClass = Ban::class;

    public string $table = 'ban b';

    public array $columns = [
        'b.id',
        'b.round_id as `round`',
        'b.server_ip',
        'b.server_port',
        'GROUP_CONCAT(r.role SEPARATOR ", ") as `role`',
        'b.bantime',
        'b.expiration_time as `expiration`',
        'b.reason',
        'b.ckey',
        'c.rank as `c_rank`',
        'b.a_ckey',
        'a.rank as `a_rank`',
        'b.unbanned_ckey',
        'u.rank as `u_rank`',
        'b.unbanned_datetime',
        'CASE
        WHEN b.expiration_time IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, b.bantime, b.expiration_time)
        ELSE 0
        END AS `minutes`',
        'CASE 
        WHEN b.expiration_time < NOW() THEN 0
        WHEN b.unbanned_ckey IS NOT NULL THEN 0
        ELSE 1 
        END as `active`',
        'b.edits',
        'GROUP_CONCAT(r.id SEPARATOR ",") as `banIds`',
    ];

    public array $where = [
        'b.ckey IS NOT NULL'
    ];

    public array $joins = [
        '`admin` AS c ON c.ckey = b.ckey',
        '`admin` AS a ON a.ckey = b.a_ckey',
        '`admin` AS u ON u.ckey = b.unbanned_ckey',
        'INNER JOIN ban r ON r.bantime = b.bantime AND r.ckey = b.ckey'
    ];

    public ?array $groupBy = [
        'b.bantime',
        'b.ckey',
        '`server_port`'
    ];

    public array $order = [
        'b.bantime DESC'
    ];

    public ?string $limit = "?, ?";

    public function getPublicBans()
    {
        return $this->run("$this->columns ORDER BY bantime DESC;");
    }

    public function getBansByCkey(string $ckey, int $page = 1, ?int $limit = null)
    {
        if(!$limit) {
            $limit = self::PER_PAGE;
        }
        $this->where[] = 'b.ckey = ?';
        $this->setPages('b.id', [$ckey]);
        $this->run($this->buildQuery(), [
            $ckey,
            ($page * $limit) - $limit,
            $limit
        ]);
        return $this->getResult();
    }

    public function getBansByAdmin(string $ckey, int $page = 1)
    {
        $this->where[] = 'b.a_ckey = ?';
        $this->setPages('b.id', [$ckey]);
        $this->run($this->buildQuery(), [
            $ckey,
            ($page * self::PER_PAGE) - self::PER_PAGE,
            self::PER_PAGE
        ]);
        return $this->getResult();
    }

    public function getBans(int $page = 1): array
    {
        $this->setPages('b.id');
        $this->run($this->buildQuery(), [
            ($page * self::PER_PAGE) - self::PER_PAGE,
            self::PER_PAGE
        ]);
        return $this->getResult();
    }

    public function getBan(int $id): Ban
    {
        $this->where[] = ('b.id = ?');
        $this->limit = null;
        $this->row($this->buildQuery(), [
            $id
        ]);
        return $this->getResult();
    }

    public function getPlayerStanding(string $ckey): array
    {
        $this->columns = [
            'b.id',
            'b.role',
            'b.expiration_time',
        ];
        $this->where = [
            'b.ckey = ?',
            '((B.expiration_time > NOW() AND B.unbanned_ckey IS NULL)
            OR (B.expiration_time IS NULL AND B.unbanned_ckey IS NULL))'
        ];
        $this->limit = null;
        $this->groupBy = null;
        $query = $this->buildQuery();
        return $this->run($query, [$ckey], true)->getResults();
    }

    public function getMostBannedRoles(): array
    {
        return $this->run("SELECT count(id) + FLOOR(2 + (RAND() * 4)) bans, 
        `role` 
        FROM ban 
        WHERE role != 'Server' 
        AND (expiration_time > NOW() OR expiration_time IS NULL) 
        GROUP BY `role` 
        ORDER BY bans DESC
         LIMIT 0, 10;");
    }

}
