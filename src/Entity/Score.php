<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $gameName = null;

    #[ORM\Column]
    private ?int $points = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $playedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getGameName(): ?string
    {
        return $this->gameName;
    }

    public function setGameName(string $gameName): static
    {
        $this->gameName = $gameName;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getPlayedAt(): ?\DateTimeInterface
    {
        return $this->playedAt;
    }

    public function setPlayedAt(\DateTimeInterface $playedAt): static
    {
        $this->playedAt = $playedAt;

        return $this;
    }
}