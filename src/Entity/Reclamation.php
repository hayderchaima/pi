<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull (message: "Il faut remplire ce chemp")]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;


     #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull (message: "Il faut remplire ce chemp")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull (message: "Il faut remplire ce chemp")]
    private ?string $fullname = null;

     #[ORM\OneToMany(mappedBy: 'reclamations', targetEntity: Reponse::class, orphanRemoval: true)]
    private Collection $reponses;


    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categories = null;



    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getCategories(): ?categorie
    {
        return $this->categories;
    }
    

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }


    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    /**
     * @return Collection<int, Reponse>
     */
    public function getreponses(): Collection
    {
        return $this->reponses;
    }

    public function addCReponseontrat( $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setReclamations($this);
        }

        return $this;
    }

    public function removeCReponseontrat( $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getReclamations() === $this) {
                $reponse->setReclamations(null);
            }
        }

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
    
    public function setCategories(?categorie $s): self
    {
        $this->categories = $s;

        return $this;
    }
}
