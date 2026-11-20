<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DuelAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null; // Wie vult dit in (Arda of Irem)

    #[ORM\Column]
    private ?int $roundNumber = null; // Welke ronde (1 t/m 10)

    #[ORM\Column]
    private ?int $questionIndex = null; // Vraagnummer (1 t/m 10)

    #[ORM\Column(type: 'text')]
    private ?string $ownAnswer = null; // Jouw eigen antwoord

    #[ORM\Column(type: 'text')]
    private ?string $guessAnswer = null; // Wat denk je dat de ander antwoordt?

    #[ORM\Column(nullable: true)]
    private ?int $pointsEarned = null; // Punten toegekend na vergelijking

    public function getId(): ?int { return $this->id; }

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(string $username): static { $this->username = $username; return $this; }

    public function getRoundNumber(): ?int { return $this->roundNumber; }
    public function setRoundNumber(int $roundNumber): static { $this->roundNumber = $roundNumber; return $this; }

    public function getQuestionIndex(): ?int { return $this->questionIndex; }
    public function setQuestionIndex(int $questionIndex): static { $this->questionIndex = $questionIndex; return $this; }

    public function getOwnAnswer(): ?string { return $this->ownAnswer; }
    public function setOwnAnswer(string $ownAnswer): static { $this->ownAnswer = $ownAnswer; return $this; }

    public function getguessAnswer(): ?string { return $this->guessAnswer; }
    public function setGuessAnswer(string $guessAnswer): static { $this->guessAnswer = $guessAnswer; return $this; }

    public function getPointsEarned(): ?int { return $this->pointsEarned; }
    public function setPointsEarned(?int $pointsEarned): static { $this->pointsEarned = $pointsEarned; return $this; }
}