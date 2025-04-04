<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CustomMediaRepository;
use App\Traits\HistoryTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ApiResource]

#[ORM\Entity(repositoryClass: CustomMediaRepository::class)]
class CustomMedia
{

    use HistoryTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $realname = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $mimeType = null;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="pictures",fileNameProperty="realpath")
     */
    private $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealname(): ?string
    {
        return $this->realname;
    }

    public function setRealname(string $realname): static
    {
        $this->realname = $realname;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFile(): ?File 
    {
        return $this->file;
    }
    
    public function setFile(?File $file): ?CustomMedia 
    {
        $this->file = $file;
        return $this;
    }
}
