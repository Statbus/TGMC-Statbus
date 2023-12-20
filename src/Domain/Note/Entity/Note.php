<?php

namespace App\Domain\Note\Entity;

use App\Domain\Note\Enum\Severity;
use App\Domain\Note\Enum\Type;
use App\Domain\Player\Data\PlayerBadge;
use DateTimeImmutable;

class Note
{
    private PlayerBadge $targetBadge;
    private PlayerBadge $adminBadge;
    private PlayerBadge $editorBadge;

    public function __construct(
        private int $id,
        private Type $type,
        private string $target,
        private string $admin,
        private string $text,
        private DateTimeImmutable $timestamp,
        private int $serverip,
        private int $port,
        private int $round,
        private bool $secret,
        private ?DateTimeImmutable $expiration,
        private ?Severity $severity,
        private ?int $playtime,
        private ?string $editor,
        private ?string $edits,
        private ?string $t_rank,
        private ?string $a_rank,
        private ?string $e_rank
    ) {
        $this->setBadges();
    }

    private function setBadges(): self
    {
        $this->setTargetBadge();
        $this->setAdminBadge();
        $this->setEditorBadge();
        return $this;
    }

    public function getTargetBadge(): PlayerBadge
    {
        return $this->targetBadge;
    }

    public function setTargetBadge(): self
    {
        $this->targetBadge = PlayerBadge::fromRank($this->getTarget(), $this->getTRank());
        return $this;
    }

    public function getAdminBadge(): PlayerBadge
    {
        return $this->adminBadge;
    }

    public function setAdminBadge(): self
    {

        $this->adminBadge = PlayerBadge::fromRank($this->getAdmin(), $this->getARank());

        return $this;
    }

    public function getEditorBadge(): PlayerBadge
    {
        return $this->editorBadge;
    }

    public function setEditorBadge(): self
    {
        if($this->getEditor()) {
            $this->editorBadge = PlayerBadge::fromRank($this->getEditor(), $this->getERank());
        }

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getAdmin(): string
    {
        return $this->admin;
    }

    public function setAdmin(string $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getServerip(): int
    {
        return $this->serverip;
    }

    public function setServerip(int $serverip): self
    {
        $this->serverip = $serverip;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getRound(): int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function isSecret(): bool
    {
        return $this->secret;
    }

    public function setSecret(bool $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getExpiration(): ?DateTimeImmutable
    {
        return $this->expiration;
    }

    public function setExpiration(?DateTimeImmutable $expiration): self
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getSeverity(): ?Severity
    {
        return $this->severity;
    }

    public function setSeverity(?Severity $severity): self
    {
        $this->severity = $severity;

        return $this;
    }

    public function getPlaytime(): ?int
    {
        return $this->playtime;
    }

    public function setPlaytime(?int $playtime): self
    {
        $this->playtime = $playtime;

        return $this;
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(?string $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getEdits(): ?string
    {
        return $this->edits;
    }

    public function setEdits(?string $edits): self
    {
        $this->edits = $edits;

        return $this;
    }

    public function getTRank(): ?string
    {
        return $this->t_rank;
    }

    public function setTRank(?string $t_rank): self
    {
        $this->t_rank = $t_rank;

        return $this;
    }

    public function getARank(): ?string
    {
        return $this->a_rank;
    }

    public function setARank(?string $a_rank): self
    {
        $this->a_rank = $a_rank;

        return $this;
    }

    public function getERank(): ?string
    {
        return $this->e_rank;
    }

    public function setERank(?string $e_rank): self
    {
        $this->e_rank = $e_rank;

        return $this;
    }
}
