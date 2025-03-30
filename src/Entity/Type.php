<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\HistoryTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    use HistoryTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 127)]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CustomMedia $image = null;

    /**
     * @var Collection<int, Card>
     */
    #[ORM\OneToMany(targetEntity: Card::class, mappedBy: 'typeId')]
    private Collection $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCustomMedia(): ?CustomMedia
    {
        return $this->image;
    }

    public function setCustomMedia(?CustomMedia $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setType($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getType() === $this) {
                $card->setType(null);
            }
        }

        return $this;
    }
}
