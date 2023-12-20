<?php

namespace App\Domain\Player\Entity;

use App\Domain\Player\Data\PlayerBadge;
use App\Enum\AdminPermissions;
use App\Service\AdminRankService;
use DateTimeImmutable;

class Player
{
    private PlayerBadge $playerBadge;

    private array $roles = [];

    public function __construct(
        private string $ckey,
        private ?string $byond_key,
        private DateTimeImmutable $firstSeen,
        private int $firstRound,
        private DateTimeImmutable $lastSeen,
        private int $lastRound,
        private int $ip,
        private string $cid,
        private ?DateTimeImmutable $accountJoinDate,
        private mixed $rank,
        private ?int $flags
    ) {
        $this->setRank();
        $this->setPlayerBadge();
        $this->setRoles();
    }


    public function getCkey(): string
    {
        return $this->ckey;
    }

    public function setCkey(string $ckey): self
    {
        $this->ckey = $ckey;

        return $this;
    }

    public function getByondKey(): ?string
    {
        return $this->byond_key;
    }

    public function setByondKey(?string $byond_key): self
    {
        $this->byond_key = $byond_key;

        return $this;
    }

    public function getFirstSeen(): DateTimeImmutable
    {
        return $this->firstSeen;
    }

    public function setFirstSeen(DateTimeImmutable $firstSeen): self
    {
        $this->firstSeen = $firstSeen;

        return $this;
    }

    public function getFirstRound(): int
    {
        return $this->firstRound;
    }

    public function setFirstRound(int $firstRound): self
    {
        $this->firstRound = $firstRound;

        return $this;
    }

    public function getLastSeen(): DateTimeImmutable
    {
        return $this->lastSeen;
    }

    public function setLastSeen(DateTimeImmutable $lastSeen): self
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    public function getLastRound(): int
    {
        return $this->lastRound;
    }

    public function setLastRound(int $lastRound): self
    {
        $this->lastRound = $lastRound;

        return $this;
    }

    public function getIp(bool $format = false): int|string
    {
        //TODO: Implement long2ip in twig extension
        if($format) {
            return long2ip($this->ip);
        }
        return $this->ip;
    }

    public function setIp(int $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getCid(): string
    {
        return $this->cid;
    }

    public function setCid(string $cid): self
    {
        $this->cid = $cid;

        return $this;
    }

    public function getAccountJoinDate(): ?DateTimeImmutable
    {
        return $this->accountJoinDate;
    }

    public function setAccountJoinDate(?DateTimeImmutable $accountJoinDate): self
    {
        $this->accountJoinDate = $accountJoinDate;

        return $this;
    }

    private function setRank(): static
    {
        $this->rank = AdminRankService::getRank($this->rank);
        return $this;
    }

    public function getRank(): array
    {
        return $this->rank;
    }

    public function getFlags(): ?int
    {
        return $this->flags;
    }

    public function setFlags(?int $flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    private function setPlayerBadge(): static
    {
        $this->playerBadge = PlayerBadge::fromArray($this->getCkey(), $this->getRank());
        return $this;
    }

    public function getPlayerBadge(): PlayerBadge
    {
        return $this->playerBadge;
    }

    private function setRoles(): self
    {
        foreach (AdminPermissions::getArray() as $p => $b) {
            if ($this->flags & $b) {
                $this->roles[$p] = true;
            }
        }
        $this->roles = array_keys($this->roles);
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
