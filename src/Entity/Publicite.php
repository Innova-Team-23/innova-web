<?php

namespace App\Entity;

use App\Repository\PubliciteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PubliciteRepository::class)]
class Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getPublicite"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["getPublicite"])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["getPublicite"])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["getPublicite"])]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getPublicite"])]
    private ?string $cta = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCta(): ?string
    {
        return $this->cta;
    }

    public function setCta(string $cta): self
    {
        $this->cta = $cta;

        return $this;
    }
}
