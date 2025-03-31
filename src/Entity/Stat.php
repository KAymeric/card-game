<?php

namespace App\Entity;

use App\Repository\StatRepository;
use App\Traits\HistoryTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['stat:read']])]
#[ORM\Entity(repositoryClass: StatRepository::class)]
class Stat
{
    use HistoryTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stat:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)] 
    #[Groups(['stat:read', 'stat:write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['stat:read', 'stat:write'])]
    private ?int $value = null;

    /**
     * @var Collection<int, card>
     */
    #[ORM\ManyToMany(targetEntity: Card::class, inversedBy: 'stats')]
    private Collection $cards;

    /**
     * @var Collection<int, set>
     */
    #[ORM\ManyToMany(targetEntity: Set::class, inversedBy: 'stats')]
    private Collection $sets;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->sets = new ArrayCollection();
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

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

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
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        $this->cards->removeElement($card);

        return $this;
    }

    /**
     * @return Collection<int, Set>
     */
    public function getSets(): Collection
    {
        return $this->sets;
    }

    public function addSet(Set $set): static
    {
        if (!$this->sets->contains($set)) {
            $this->sets->add($set);
        }

        return $this;
    }

    public function removeSet(Set $set): static
    {
        $this->sets->removeElement($set);

        return $this;
    }
}
