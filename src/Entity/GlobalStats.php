<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GlobalStatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GlobalStatsRepository::class)]
class GlobalStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $key = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Increment the value.
     * Converts the current value (stored as string) to int, increments it, and saves it back as string.
     */
    public function incrementValue(int $delta = 1): static
    {
        $currentValue = (int)$this->value;
        $currentValue += $delta;
        $this->value = (string)$currentValue;
        return $this;
    }
}
