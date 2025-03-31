<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CardRepository;
use App\Traits\HistoryTrait;
use Doctrine\ORM\Mapping as ORM;
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

    #[ORM\ManyToOne(inversedBy: 'set')]
    private ?Type $type = null;

    #[Groups(['card:read'])]
    #[ORM\ManyToOne(targetEntity: Set::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(name: 'set_id', referencedColumnName: 'id')]
    private ?Set $set = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CustomMedia $image = null;

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
}
