<?php

namespace App\Domain\Stat\Entity;

use App\Domain\Stat\Enum\Type;
use DateTimeImmutable;

class Stat
{
    private mixed $data;

    public function __construct(
        private int $id,
        private int $round,
        private string $name,
        private int $version,
        private ?Type $type,
        private string $json
    ) {
        $this->data = (array) json_decode($this->json, true)['data'];
        if($this->type === Type::TALLY) {
            ksort($this->data, SORT_STRING);
        }
        if($this->type === Type::AMOUNT) {
            $this->data = $this->data[0];
        }
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getRound(): int
    {
        return $this->round;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function getJson(): string
    {
        return $this->json;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
