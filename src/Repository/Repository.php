<?php

namespace App\Repository;

use ParagonIE\EasyDB\EasyDB;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Repository
{
    private EasyDB $db;

    public const PER_PAGE = 60;

    private mixed $result;

    public ?string $entityClass = null;

    protected array $entityMetadata = [];

    public string $table;

    public array $columns = [];

    public array $where = [];

    public array $joins = [];

    public array $order = [];

    public ?array $groupBy = [];

    public ?string $limit = '0, 100';

    private int $pages = 0;

    public function __construct(
        protected ContainerInterface $container,
    ) {
        $this->db = $container->get(EasyDB::class);
        $this->fetchEntityMetadata();
    }

    public function run(
        string $statement,
        array $params = [],
        bool $skipParse = false
    ): static {
        $this->setResults($this->db->run($statement, ...$params), $skipParse);

        return $this;
    }

    public function row(
        string $statement,
        array $params = [],
        bool $skipParse = false
    ): static {
        $this->setResult($this->db->row($statement, ...$params), $skipParse);
        return $this;
    }

    public function directQuery(
        string $table,
        array $columns,
        array $where = [],
        array $joins = [],
        array $order = [],
        array $group = [],
        ?string $limit = null
    ) {
        $this->table = $table;
        $this->columns = $columns;
        $this->where = $where;
        $this->joins = $joins;
        $this->order = $order;
        $this->groupBy = $group;
        $this->limit = $limit;
        return $this->buildQuery();
    }

    public function buildQuery(): string
    {
        $columns = implode(",\n", $this->columns);
        $where = implode("\n AND ", $this->where);
        $joins = $this->processJoins();
        $order = $this->processOrder();
        $group = $this->processGroupBy();
        $query = sprintf(
            "SELECT %s \nFROM %s \n%s %s %s %s \n%s",
            $columns,
            $this->table,
            $joins,
            $where ? "WHERE $where" : null,
            $group ? "GROUP BY $group" : null,
            $order ? "ORDER BY $order" : null,
            $this->limit ? "LIMIT $this->limit" : null,
        );
        return $query;
    }

    private function processJoins(): string
    {
        if($this->joins) {
            $joinLine = '';
            foreach ($this->joins as $j) {
                $j = str_replace('LEFT JOIN ', '', $j);
                if(str_contains($j, ' JOIN')) {
                    $joinLine .= "$j\n";
                } else {
                    $joinLine .= "LEFT JOIN $j\n";
                }
            }
            $joins = $joinLine;
        } else {
            $joins = '';
        }
        return $joins;
    }

    private function processOrder(): string
    {
        if($this->order) {
            $order = implode("\n AND ", $this->order);
            return $order;
        }
        return '';
    }

    private function processGroupBy(): ?string
    {
        if($this->groupBy) {
            return implode(", ", $this->groupBy);
        }
        return null;
    }

    private function setResult(mixed $data, bool $skipParse = false): static
    {
        if(isset($this->entityClass) && !$skipParse) {
            $this->result = new $this->entityClass(...$this->processRow($data));
        } else {
            $this->result = $data;
        }
        return $this;
    }

    private function setResults(array $data, bool $skipParse = false): static
    {
        $this->result = [];
        if(isset($this->entityClass) && !$skipParse) {
            foreach($data as $row) {
                $this->result[] = new $this->entityClass(...$this->processRow($row));
            }
        } else {
            $this->result = $data;
        }
        return $this;
    }

    private function processRow(array $row): array
    {
        foreach($row as $k => &$d) {
            if(in_array($this->entityMetadata[$k], ['DateTime','DateTimeImmutable']) && $d) {
                $d = new $this->entityMetadata[$k]($d);
            }
            if(str_contains($this->entityMetadata[$k], 'Enum') && $d) {
                $d = $this->castToEnum($this->entityMetadata[$k], $d);
            }
        }
        return $row;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function getResults(): array
    {
        return $this->result;
    }

    private function castToEnum(string $enum, string $value)
    {
        return call_user_func($enum."::tryFrom", $value);
    }

    public function setPages(string $field = 'id', array $params = []): static
    {
        //TODO: Refactor to use buildQuery
        $query = sprintf(
            "SELECT count(%s) FROM %s %s",
            $field,
            $this->table,
            $this->where ? "WHERE ". implode("\n AND ", $this->where) : null
        );
        // var_dump($query);
        $count = $this->db->cell(
            $query,
            ...$params
        );
        $this->pages = ceil($count / self::PER_PAGE);
        return $this;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    private function fetchEntityMetadata(): static
    {
        if($this->entityClass) {
            $properties = new ReflectionClass($this->entityClass);
            foreach($properties->getProperties() as $p) {
                $this->entityMetadata[$p->getName()] = $p->getType()->getName();
            }
        }
        return $this;
    }

}
