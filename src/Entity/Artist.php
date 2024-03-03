<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "nom artist ne peux pas être vide! ")]
    #[Assert\Length(
        min: 3,
        max: 10,
        minMessage: "Le nom doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le nom doit comporter au plus {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "nom artist ne peux pas être vide! ")]
    private ?string $nationalite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "nom artist ne peux pas être vide! ")]
    private ?string $biography = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThan("1999-01-01", message:"date naissance doit etre inferieur a 1999-01-01.")]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\OneToMany(targetEntity: Oeuvre::class, mappedBy: 'artist')]
    #[Groups(["artist"])]
    private Collection $oeuvres;

    #[ORM\Column(length: 255)]
    private ?string $imageArtist = null;

    public function __construct()
    {
        $this->oeuvres = new ArrayCollection();
    }

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

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): static
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): static
    {
        $this->biography = $biography;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    /**
     * @return Collection<int, Oeuvre>
     */
    public function getOeuvres(): Collection
    {
        return $this->oeuvres;
    }

    public function addOeuvre(Oeuvre $oeuvre): static
    {
        if (!$this->oeuvres->contains($oeuvre)) {
            $this->oeuvres->add($oeuvre);
            $oeuvre->setArtist($this);
        }

        return $this;
    }

    public function removeOeuvre(Oeuvre $oeuvre): static
    {
        if ($this->oeuvres->removeElement($oeuvre)) {
            // set the owning side to null (unless already changed)
            if ($oeuvre->getArtist() === $this) {
                $oeuvre->setArtist(null);
            }
        }

        return $this;
    }

    public function getImageArtist(): ?string
    {
        return $this->imageArtist;
    }

    public function setImageArtist(string $imageArtist): static
    {
        $this->imageArtist = $imageArtist;

        return $this;
    }
    public function __serialize(): array
    {
        return [];
    }
}
