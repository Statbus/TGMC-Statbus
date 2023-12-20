<?php

namespace App\Domain\Round\Repository;

use App\Domain\Round\Entity\Round;
use App\Repository\Repository;

class RoundRepository extends Repository
{
    private array $currentRounds = [];

    public ?string $entityClass = Round::class;

    public string $table = 'round r';

    public array $columns = [
        'r.id',
        'r.initialize_datetime as `initDateTime`',
        'r.start_datetime as `startDateTime`',
        'r.shutdown_datetime as `shutdownDateTime`',
        'r.end_datetime as `endDateTime`',
        'r.server_ip as `server`',
        'r.server_port as `port`',
        'r.commit_hash as `commit`',
        'r.game_mode as `mode`',
        'r.game_mode_result as `result`',
        'r.map_name as `map`'
    ];

    public array $order = [
        'r.initialize_datetime DESC'
    ];

    // public array $where = [
    //     'NOT IN '.implode($this->current_rounds)
    // ];

    public ?string $limit = "?,?";

    private function getCurrentRounds(): static
    {
        $this->currentRounds = $this->container->get('rounds');
        return $this;
    }

    public function getRounds(int $page = 1, ?int $limit = null): array
    {
        if(!$limit) {
            $limit = self::PER_PAGE;
        }
        $this->getCurrentRounds();
        $this->where[] = 'r.id NOT IN ('.implode(',', $this->currentRounds).")";
        // var_dump($this->buildQuery());
        $this->setPages('r.id');
        $this->run($this->buildQuery(), [
            ($page * self::PER_PAGE) - self::PER_PAGE,
            self::PER_PAGE
        ]);
        return $this->getResults();
    }

    public function getRound(int $id): Round
    {
        $this->where[] = 'r.id = ?';
        $this->getCurrentRounds();
        $this->where[] = 'r.id NOT IN ('.implode(',', $this->currentRounds).")";
        $this->limit = null;
        $this->row($this->buildQuery(), [
            $id
        ]);
        return $this->getResult();
    }

}
