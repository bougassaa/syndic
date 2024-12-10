<?php

namespace App\Entity;

use App\Repository\AppartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppartementRepository::class)]
class Appartement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\ManyToOne(inversedBy: 'appartements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Batiment $batiment = null;

    /**
     * @var Collection<int, Proprietaire>
     */
    #[ORM\OneToMany(targetEntity: Proprietaire::class, mappedBy: 'appartement')]
    private Collection $proprietaires;

    /**
     * @var Collection<int, Cotisation>
     */
    #[ORM\OneToMany(targetEntity: Cotisation::class, mappedBy: 'appartement')]
    private Collection $cotisations;

    public function __construct()
    {
        $this->proprietaires = new ArrayCollection();
        $this->cotisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getBatiment(): ?Batiment
    {
        return $this->batiment;
    }

    public function setBatiment(?Batiment $batiment): static
    {
        $this->batiment = $batiment;

        return $this;
    }

    /**
     * @return Collection<int, Proprietaire>
     */
    public function getProprietaires(): Collection
    {
        return $this->proprietaires;
    }

    public function addProprietaire(Proprietaire $proprietaire): static
    {
        if (!$this->proprietaires->contains($proprietaire)) {
            $this->proprietaires->add($proprietaire);
            $proprietaire->setAppartement($this);
        }

        return $this;
    }

    public function removeProprietaire(Proprietaire $proprietaire): static
    {
        if ($this->proprietaires->removeElement($proprietaire)) {
            // set the owning side to null (unless already changed)
            if ($proprietaire->getAppartement() === $this) {
                $proprietaire->setAppartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisation>
     */
    public function getCotisations(): Collection
    {
        return $this->cotisations;
    }

    public function addCotisation(Cotisation $cotisation): static
    {
        if (!$this->cotisations->contains($cotisation)) {
            $this->cotisations->add($cotisation);
            $cotisation->setAppartement($this);
        }

        return $this;
    }

    public function removeCotisation(Cotisation $cotisation): static
    {
        if ($this->cotisations->removeElement($cotisation)) {
            // set the owning side to null (unless already changed)
            if ($cotisation->getAppartement() === $this) {
                $cotisation->setAppartement(null);
            }
        }

        return $this;
    }

    public function getAbsoluteName(): string
    {
        return $this->getBatiment()->getSyndic()->getNom() . ' ' .
            $this->getBatiment()->getNom() . '-' .
            $this->getNumero();
    }
}
