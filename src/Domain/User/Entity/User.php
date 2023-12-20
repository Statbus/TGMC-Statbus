<?php

namespace App\Domain\User\Entity;

use App\Domain\Player\Data\PlayerBadge;
use App\Enum\AdminPermissions;
use App\Service\AdminRankService;
use DateTimeImmutable;

class User
{
    private ?string $source = null;

    private PlayerBadge $playerBadge;

    private array $roles = [];

    public function __construct(
        private DateTimeImmutable $lastSeen,
        private string $ckey,
        private string $byondKey,
        private int $ip,
        private mixed $rank = 'Player',
        private int $flags = 0,
    ) {
        $this->setRank();
        $this->setPlayerBadge();
        $this->setRoles();
    }

    public function getCkey(): string
    {
        return $this->ckey;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;
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

    private function setPlayerBadge(): static
    {
        $this->playerBadge = PlayerBadge::fromArray($this->getCkey(), $this->getRank());
        return $this;
    }

    public function getPlayerBadge(): PlayerBadge
    {
        return $this->playerBadge;
    }

    public function getSource(): string
    {
        return $this->source;
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

    public function has(string $role): bool
    {
        if(in_array($role, $this->roles)) {
            return true;
        }
        return false;
    }
}
