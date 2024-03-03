<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\OeuvreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OeuvreRepository::class)]
class Oeuvre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "titre oeuvre ne peux pas être vide! ")]
    #[Assert\Length(
        min: 3,
        max: 10,
        minMessage: "Le titre doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le titre doit comporter au plus {{ limit }} caractères."
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "description oeuvre ne peux pas être vide! ")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "date oeuvre ne peux pas être vide! ")]
    #[Assert\LessThan("today", message: "date doit etre inferieur a aujourd'hui.")]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "type oeuvre ne peux pas être vide! ")]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'oeuvres')]
    private ?Artist $artist = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "image oeuvre ne peux pas être vide! ")]
   
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
