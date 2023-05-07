<?php

namespace App\Entity;

use App\Repository\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationRepository::class)]
class Translation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $key = null;

    #[ORM\OneToMany(targetEntity: TranslationText::class, mappedBy: 'translation')]
    private $translationTexts;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getTextForLanguage(string $language): ?string
{
    foreach ($this->translationTexts as $translationText) {
        if ($translationText->getLanguage() === $language) {
            return $translationText->getText();
        }
    }
    return null;
}
}
