<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CardRepository;
use App\Traits\HistoryTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['card:read']])]
#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{

    use HistoryTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['card:read', 'set:read'])]
    private ?int $id = null;
    
    #[Groups(['card:read', 'set:read'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['card:read', 'set:read', 'card:write'])]
    #[ORM\Column(length: 255)]
    private ?string $description = null;
    
    #[Groups(['card:read', 'type:read', 'card:write'])]
    #[ORM\ManyToOne(targetEntity: Type::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'cards')]
    private ?Type $type = null;
    
    #[Groups(['card:read'])]
    #[ORM\ManyToOne(targetEntity: Set::class, fetch: 'EAGER', inversedBy: 'cards')]
    #[ORM\JoinColumn(name: 'set_id', referencedColumnName: 'id')]
    private ?Set $set = null;
    
    /**
     * @var Collection<int, stat>
     */
    #[Groups(['card:read', 'card:write'])]
    #[ORM\ManyToMany(targetEntity: Stat::class, inversedBy: 'cards')]
    private Collection $stats;
    
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CustomMedia $image = null;
    
    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
        $this->stats = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSet(): ?Set
    {
        return $this->set;
    }

    public function setSet(?Set $set): static
    {
        $this->set = $set;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

        /**
     * @return Collection<int, Stat>
     */
    public function getStats(): Collection
    {
        return $this->stats;
    }

    public function addStat(Stat $stats): static
    {
        if (!$this->stats->contains($stats)) {
            $this->stats->add($stats);
        }

        return $this;
    }

    public function removeStat(Stat $stats): static
    {
        $this->stats->removeElement($stats);

        return $this;
    }
}
