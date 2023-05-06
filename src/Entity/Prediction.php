<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PredictionRepository::class)]
class Prediction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getPredicts"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["getPredicts"])]
    private ?string $description = null;
    
    #[Groups(["getPredicts"])]
    private $idDream;

    #[ORM\OneToOne(targetEntity: Dream::class)]
    #[ORM\JoinColumn(name: "id_dream", referencedColumnName: "id")]
    private $dream;

    public function getDream(): ?Dream
    {
        return $this->dream;
    }

    public function setDream(Dream $dream): self
    {
        $this->dream = $dream;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdDream(): ?int
    {
        return $this->idDream;
    }

    public function setIdDream(int $idDream): self
    {
        $this->idDream = $idDream;
        
        return $this;
    }
}
