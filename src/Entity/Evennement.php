<?php

namespace App\Entity;

use App\Repository\EvennementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EvennementRepository::class)]
class Evennement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\NotBlank(message: "nom evennement ne peux pas être vide! ")]
    #[Assert\Length(
        min: 3,
        max: 10,
        minMessage: "The name must be at least {{ limit }} characters long",
        maxMessage: "The name cannot be longer than {{ limit }} characters"
    )]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;
    #[Assert\GreaterThan("today", message: "date inferieur a aujourd'hui.")]
    #[Assert\NotBlank(message: "date debut evennement ne peux pas être vide! ")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;
    #[Assert\NotBlank(message: "date fin evennement ne peux pas être vide! ")]
    #[Assert\NotNull(message: "date debut evennement ne peux pas être vide! ")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "lieu evennement ne peux pas être vide! ")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "lieu doit depasser {{ limit }} characteres",
        maxMessage: "lieu ne doit pas depasser {{ limit }} characteres"
    )]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Assert\Positive(message: "nombre participant doit etre positive! ")]
    private ?int $nbParticipant = null;

    #[ORM\ManyToOne(inversedBy: 'evennements')]
    #[Assert\NotBlank(message: "sponsor evennement ne peux pas être vide! ")]
    private ?Sponsor $sponsor = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "image evennement ne peux pas être vide! ")]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getNbParticipant(): ?int
    {
        return $this->nbParticipant;
    }

    public function setNbParticipant(int $nbParticipant): static
    {
        $this->nbParticipant = $nbParticipant;

        return $this;
    }

    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    public function setSponsor(?Sponsor $sponsor): static
    {
        $this->sponsor = $sponsor;

        return $this;
    }
    public function __toString()
    {
        return $this->nom;
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
