<?php

namespace App\Domain\Note\Repository;

use App\Domain\Note\Entity\Note;
use App\Repository\Repository;

class NoteRepository extends Repository
{
    public ?string $entityClass = Note::class;

    public string $table = 'messages n';

    public array $columns = [
        'n.id',
        'n.type',
        'n.targetckey as `target`',
        'n.adminckey as `admin`',
        'n.text',
        'n.timestamp',
        'n.server_ip as `serverip`',
        'n.server_port as `port`',
        'n.round_id as `round`',
        'n.secret',
        'n.expire_timestamp as `expiration`',
        'n.severity',
        'n.playtime',
        'n.lasteditor as `editor`',
        'n.edits',
        't.rank as t_rank',
        'a.rank as a_rank',
        'e.rank as e_rank',
    ];

    public array $joins = [
        'LEFT JOIN admin t ON n.targetckey = t.ckey',
        'LEFT JOIN admin a ON n.adminckey = a.ckey',
        'LEFT JOIN admin e ON n.lasteditor = e.ckey'
    ];

    public array $where = [
        "n.deleted = 0",
        "n.type != 'memo'",
    ];

    public array $order = [
        'n.timestamp DESC'
    ];

    public ?string $limit = "?, ?";

    public function getNotes(int $page = 1): array
    {
        $this->setPages('n.id');
        $this->run($this->buildQuery(), [
            ($page * self::PER_PAGE) - self::PER_PAGE,
            self::PER_PAGE
        ]);
        return $this->getResult();

    }

    public function getNotesForCkey(string $ckey, bool $allowSecret = false, int $page = 1, ?int $limit = null): array
    {
        if(!$limit) {
            $limit = self::PER_PAGE;
        }
        $this->where[] = "n.targetckey = ?";
        if(!$allowSecret) {
            $this->where[] = "n.secret = 0";
        }
        $this->setPages('n.id', [$ckey]);
        $this->run($this->buildQuery(), [
            $ckey,
            ($page * $limit) - $limit,
            $limit
        ]);
        return $this->getResult();
    }

    public function getNotesByAdmin(string $ckey, int $page = 1): array
    {
        $this->where[] = "n.adminckey = ?";
        $this->setPages('n.id', [$ckey]);
        $this->run($this->buildQuery(), [
            $ckey,
            ($page * self::PER_PAGE) - self::PER_PAGE,
            self::PER_PAGE
        ]);
        return $this->getResult();
    }

    public function getNoteById(int $id): Note
    {
        $this->where[] = "n.id = ?";
        $this->limit = null;
        $this->row($this->buildQuery(), [
            $id,
        ]);
        return $this->getResult();
    }

}
