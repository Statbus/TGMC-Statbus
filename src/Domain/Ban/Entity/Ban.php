<?php

namespace App\Domain\Ban\Entity;

use App\Domain\Ban\Enum\Status;
use App\Domain\Player\Data\PlayerBadge;

use DateTime;
use DateTimeImmutable;

class Ban
{
    public bool $roleBans = false;

    private ?PlayerBadge $playerBadge = null;
    private ?PlayerBadge $adminBadge = null;
    private ?PlayerBadge $unbannerBadge = null;

    private Status $status = Status::ACTIVE;

    public static function fromDb(object $row)
    {
        return new self(
            ...get_object_vars($row)
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(...$data);
    }

    public function __construct(
        private int $id,
        private int $round,
        private int $server_ip,
        private int $server_port,
        private mixed $role,
        private ?DateTime $bantime,
        private ?DateTime $expiration,
        private ?string $reason,
        private ?string $ckey,
        private ?string $c_rank,
        private ?string $a_ckey,
        private ?string $a_rank,
        private ?string $unbanned_ckey,
        private ?string $u_rank,
        private ?DateTime $unbanned_datetime,
        private ?int $minutes,
        private bool $active,
        private mixed $edits,
        private mixed $banIds,
    ) {
        $this->roleBans();
        $this->splitEdits();
        $this->setPlayerBadge();
        $this->setAdminBadge();
        $this->setUnbannerBadge();
        $this->setStatus();
        $this->setBanIds();
        // $this->setServerInfo();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBantime(): DateTime
    {
        return $this->bantime;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getIp()
    {
        return $this->server_ip;
    }

    public function getPort()
    {
        return $this->server_port;
    }

    private function roleBans()
    {
        if (str_contains($this->role, ', ')) {
            $this->roleBans = true;
            $this->role = explode(', ', $this->role);
        } else {
            $role = $this->role;
            $this->role = [];
            $this->role[] = $role;
        }
        $this->role = array_unique($this->role);
    }
    private function splitEdits()
    {
        if ($this->edits) {
            $this->edits = explode("<br>to<br>", $this->edits);
        }
    }

    private function setAdminBadge(): self
    {
        if(!$this->a_rank) {
            $this->a_rank = 'Player';
        }
        $this->adminBadge = PlayerBadge::fromRank($this->a_ckey, $this->a_rank);
        return $this;
    }

    public function getAdminBadge(): ?PlayerBadge
    {
        return $this->adminBadge;
    }

    private function setPlayerBadge(): self
    {
        if(!$this->c_rank) {
            $this->c_rank = 'Player';
        }
        $this->playerBadge = PlayerBadge::fromRank($this->ckey, $this->c_rank);
        return $this;
    }

    public function getPlayerBadge(): ?PlayerBadge
    {
        return $this->playerBadge;
    }

    private function setUnbannerBadge(): self
    {
        if(!$this->unbanned_ckey) {
            $this->unbannerBadge = null;
            return $this;
        }
        if(!$this->u_rank) {
            $this->u_rank = 'Player';
        }
        $this->unbannerBadge = PlayerBadge::fromRank($this->unbanned_ckey, $this->u_rank);
        return $this;
    }

    public function getUnbannerBadge(): ?PlayerBadge
    {
        return $this->unbannerBadge;
    }

    public function setStatus(): self
    {
        if($this->unbanned_ckey) {
            $this->status = Status::LIFTED;
            return $this;
        }
        if(!$this->expiration) {
            $this->status = Status::PERMANENT;
            return $this;
        }
        if($this->expiration > new DateTime()) {
            $this->status = Status::ACTIVE;
            return $this;
        } else {
            $this->status = Status::EXPIRED;
            return $this;
        }
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getCkey(): string
    {
        return $this->ckey;
    }

    public function getRole(): array
    {
        return $this->role;
    }

    public function getExpiration(): ?DateTime
    {
        return $this->expiration;
    }

    public function setBanIds(): static
    {
        $this->banIds = explode(",", $this->banIds);
        return $this;
    }

    public function getBanIds(): ?array
    {
        return $this->banIds;
    }

    public function getAdmin(): string
    {
        return $this->a_ckey;
    }

}
