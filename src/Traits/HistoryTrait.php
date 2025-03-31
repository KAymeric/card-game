<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HistoryTrait {
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function setUpdatedAt(string|\DateTimeImmutable $updatedAt): static
    {
        if (!$updatedAt instanceof \DateTimeImmutable) {
            $updatedAt = new \DateTimeImmutable($updatedAt);
        }

        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
