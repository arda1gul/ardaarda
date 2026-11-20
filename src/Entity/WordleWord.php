<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WordleWord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $setterUsername = null; // Wie heeft het woord bedacht

    #[ORM\Column(length: 50)]
    private ?string $guesserUsername = null; // Wie moet het raden

    #[ORM\Column(length: 50)]
    private ?string $word = null; // Het geheime woord

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $targetDate = null; // Datum van vandaag

    #[ORM\Column]
    private bool $isGuessed = false; // Is het geraad?

    #[ORM\Column]
    private bool $isCompleted = true; // Is de pogingen-ronde klaar?

    public function getId(): ?int { return $this->id; }

    public function getSetterUsername(): ?string { return $this->setterUsername; }
    public function setSetterUsername(string $setterUsername): static { $this->setterUsername = $setterUsername; return $this; }

    public function getGuesserUsername(): ?string { return $this->guesserUsername; }
    public function setGuesserUsername(string $guesserUsername): static { $this->guesserUsername = $guesserUsername; return $this; }

    public function getWord(): ?string { return $this->word; }
    public function setWord(string $word): static { $this->word = strtolower(trim($word)); return $this; }

    public function getTargetDate(): ?\DateTimeInterface { return $this->targetDate; }
    public function setTargetDate(\DateTimeInterface $targetDate): static { $this->targetDate = $targetDate; return $this; }

    public function isGuessed(): bool { return $this->isGuessed; }
    public function setIsGuessed(bool $isGuessed): static { $this->isGuessed = $isGuessed; return $this; }

    public function isCompleted(): bool { return $this->isCompleted; }
    public function setIsCompleted(bool $isCompleted): static { $this->isCompleted = $isCompleted; return $this; }
}