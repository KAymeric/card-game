<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SetRepository;
use App\Traits\HistoryTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(normalizationContext: ['groups' => ['set:read']])]
#[ORM\Entity(repositoryClass: SetRepository::class)]
class Set
{

    use HistoryTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['set:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['set:read'])]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CustomMedia $image = null;

    /**
     * @var Collection<int, Card>
     */
    #[ORM\OneToMany(targetEntity: Card::class, mappedBy: 'set', orphanRemoval: true)]
    #[Groups(['set:read'])]
    private Collection $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();

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

    public function getImage(): ?CustomMedia
    {
        return $this->image;
    }

    public function setImage(?CustomMedia $image): static
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
            $card->setSet($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getSet() === $this) {
                $card->setSet(null);
            }
        }

        return $this;
    }
}
