<?php

namespace App\Domain\Round\Entity;

use DateInterval;
use DateTimeImmutable;

class Round
{
    public function __construct(
        private int $id,
        private DateTimeImmutable $initDateTime,
        private ?DateTimeImmutable $startDateTime,
        private ?DateTimeImmutable $shutdownDateTime,
        private ?DateTimeImmutable $endDateTime,
        private int $server,
        private int $port,
        private ?string $commit,
        private ?string $mode,
        private ?string $result,
        private ?string $map
    ) {

    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * getStart
     *
     * Returns the start datetime (if set), or the init datetime otherwise
     *
     * @return DateTimeImmutable
     */
    public function getStart(): \DateTimeImmutable
    {
        if($this->getStartDatetime()) {
            return $this->getStartDatetime();
        }
        return $this->getInitDateTime();
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        if($this->getEndDatetime()) {
            return $this->getEndDatetime();
        }
        return $this->getShutdownDatetime();
    }


    public function getStartDateTime(): ?DateTimeImmutable
    {
        return $this->startDateTime;
    }

    public function getShutdownDateTime(): ?DateTimeImmutable
    {
        return $this->shutdownDateTime;
    }

    public function getEndDateTime(): ?DateTimeImmutable
    {
        return $this->endDateTime;
    }

    public function getDuration(): bool|DateInterval
    {
        if($this->getStart() && $this->getEnd()) {
            return $this->getStart()->diff($this->getEnd());
        }
        return false;
    }

    public function getStartDuration(): bool|DateInterval
    {
        if($this->getInitDateTime() && $this->getStartDateTime()) {
            return $this->getInitDateTime()->diff($this->getStartDatetime());
        }
        return false;
    }

    public function getEndDuration(): bool|DateInterval
    {
        if($this->getEndDateTime() && $this->getShutdownDateTime()) {
            return $this->getEndDateTime()->diff($this->getShutdownDateTime());
        }
        return false;
    }

    public function getServer(): int
    {
        return $this->server;
    }

    public function getInitDateTime(): DateTimeImmutable
    {
        return $this->initDateTime;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function getResult(): string
    {
        if(!$this->result) {
            return 'N/A';
        }
        return $this->result;
    }

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function getResultClass(): array
    {
        $result = [
            'class' => 'info',
        ];

        if(str_contains($this->getResult(), 'Xenomorph')) {
            $result['class'] = 'danger';
        } elseif (str_contains($this->getResult(), 'Marine')) {
            $result['class'] = 'success';
        } else {
            $result['class'] = 'warning';
        }
        // if(str_contains($this->getResult(), 'Minor')) {
        //     $result['class'] = $result['class'] ." bg-opacity-50";
        // }

        return $result;
    }

    public function crashed(): bool
    {
        if(!$this->getDuration() && !$this->getShutdownDatetime()) {
            return true;
        }
        return false;
    }

}
