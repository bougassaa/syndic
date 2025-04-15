<?php

namespace App\Entity;

use App\Repository\BatimentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BatimentRepository::class)]
class Batiment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Appartement>
     */
    #[ORM\OneToMany(targetEntity: Appartement::class, mappedBy: 'batiment')]
    private Collection $appartements;

    #[ORM\ManyToOne(inversedBy: 'batiments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Syndic $syndic = null;

    public function __construct()
    {
        $this->appartements = new ArrayCollection();
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

    /**
     * @return Collection<int, Appartement>
     */
    public function getAppartements(): Collection
    {
        return $this->appartements;
    }

    public function addAppartement(Appartement $appartement): static
    {
        if (!$this->appartements->contains($appartement)) {
            $this->appartements->add($appartement);
            $appartement->setBatiment($this);
        }

        return $this;
    }

    public function removeAppartement(Appartement $appartement): static
    {
        if ($this->appartements->removeElement($appartement)) {
            // set the owning side to null (unless already changed)
            if ($appartement->getBatiment() === $this) {
                $appartement->setBatiment(null);
            }
        }

        return $this;
    }

    public function getSyndic(): ?Syndic
    {
        return $this->syndic;
    }

    public function setSyndic(?Syndic $syndic): static
    {
        $this->syndic = $syndic;

        return $this;
    }

    public function getOccupedAppartements(): array
    {
        return $this->appartements->filter(function (Appartement $a) {
            $lasProprietaire = $a->getLastProprietaire();
            return $lasProprietaire && !$lasProprietaire->isSystem();
        })->map(function (Appartement $a) {
            return ['value' => $a->getId(), 'text' => $a->getAbsoluteName(false)];
        })->toArray();
    }
}
