<?php

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "nom ne peux pas être vide! ")]
    #[Assert\Length(
        min: 3,
        max: 10,
        minMessage: "nom doit depasser {{ limit }} characteres",
        maxMessage: "nom ne doit pas depasser {{ limit }} characteres"
    )]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\Positive(message: "montant doit etre positive! ")]
    private ?float $montant = null;

    #[ORM\OneToMany(targetEntity: Evennement::class, mappedBy: 'sponsor')]
    private Collection $evennements;

    public function __construct()
    {
        $this->evennements = new ArrayCollection();
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

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return Collection<int, Evennement>
     */
    public function getEvennements(): Collection
    {
        return $this->evennements;
    }

    public function addEvennement(Evennement $evennement): static
    {
        if (!$this->evennements->contains($evennement)) {
            $this->evennements->add($evennement);
            $evennement->setSponsor($this);
        }

        return $this;
    }

    public function removeEvennement(Evennement $evennement): static
    {
        if ($this->evennements->removeElement($evennement)) {
            // set the owning side to null (unless already changed)
            if ($evennement->getSponsor() === $this) {
                $evennement->setSponsor(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->nom;
    }
}
