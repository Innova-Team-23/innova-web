<?php

namespace App\Entity;

use App\Repository\TranslationTextRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationTextRepository::class)]
class TranslationText
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $language = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

   
    #[ORM\ManyToOne(targetEntity: Translation::class)]
    #[ORM\JoinColumn(name: "translation_id", referencedColumnName: "id")]
    private $translation;

    /**
     * Get the value of translation
     */
    public function getTranslation(): ?Translation
    {
        return $this->translation;
    }

    /**
     * Set the value of translation
     *
     * @param Translation $translation
     * @return self
     */
    public function setTranslation(?Translation $translation): self
    {
        $this->translation = $translation;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
