<?php

namespace App\Entity;

use App\Repository\UserPubliciteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPubliciteRepository::class)]
class UserPublicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "id_users", referencedColumnName: "id")]
    private $user;

    #[ORM\ManyToOne(targetEntity: Publicite::class)]
    #[ORM\JoinColumn(name: "id_publicites", referencedColumnName: "id")]
    private $publicite;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPublicite(): ?Publicite
    {
        return $this->publicite;
    }

    public function setPublicite(?Publicite $publicite): self
    {
        $this->publicite = $publicite;

        return $this;
    }

}
