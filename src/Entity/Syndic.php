<?php

namespace App\Entity;

use App\Repository\SyndicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SyndicRepository::class)]
class Syndic
{

    const GH_15 = 'GH15';
    const GH_16 = 'GH16';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Batiment>
     */
    #[ORM\OneToMany(targetEntity: Batiment::class, mappedBy: 'syndic')]
    private Collection $batiments;

    /**
     * @var Collection<int, Depense>
     */
    #[ORM\OneToMany(targetEntity: Depense::class, mappedBy: 'syndic')]
    private Collection $depenses;

    /**
     * @var Collection<int, Tarif>
     */
    #[ORM\OneToMany(targetEntity: Tarif::class, mappedBy: 'syndic')]
    private Collection $tarifs;

    /**
     * @var Collection<int, TypeDepense>
     */
    #[ORM\OneToMany(targetEntity: TypeDepense::class, mappedBy: 'syndic')]
    private Collection $typeDepenses;

    public function __construct()
    {
        $this->batiments = new ArrayCollection();
        $this->depenses = new ArrayCollection();
        $this->tarifs = new ArrayCollection();
        $this->typeDepenses = new ArrayCollection();
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
     * @return Collection<int, Batiment>
     */
    public function getBatiments(): Collection
    {
        return $this->batiments;
    }

    public function addBatiment(Batiment $batiment): static
    {
        if (!$this->batiments->contains($batiment)) {
            $this->batiments->add($batiment);
            $batiment->setSyndic($this);
        }

        return $this;
    }

    public function removeBatiment(Batiment $batiment): static
    {
        if ($this->batiments->removeElement($batiment)) {
            // set the owning side to null (unless already changed)
            if ($batiment->getSyndic() === $this) {
                $batiment->setSyndic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depense>
     */
    public function getDepenses(): Collection
    {
        return $this->depenses;
    }

    public function addDepense(Depense $depense): static
    {
        if (!$this->depenses->contains($depense)) {
            $this->depenses->add($depense);
            $depense->setSyndic($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): static
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getSyndic() === $this) {
                $depense->setSyndic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tarif>
     */
    public function getTarifs(): Collection
    {
        return $this->tarifs;
    }

    public function addTarif(Tarif $tarif): static
    {
        if (!$this->tarifs->contains($tarif)) {
            $this->tarifs->add($tarif);
            $tarif->setSyndic($this);
        }

        return $this;
    }

    public function removeTarif(Tarif $tarif): static
    {
        if ($this->tarifs->removeElement($tarif)) {
            // set the owning side to null (unless already changed)
            if ($tarif->getSyndic() === $this) {
                $tarif->setSyndic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeDepense>
     */
    public function getTypeDepenses(): Collection
    {
        return $this->typeDepenses;
    }

    public function addTypeDepense(TypeDepense $typeDepense): static
    {
        if (!$this->typeDepenses->contains($typeDepense)) {
            $this->typeDepenses->add($typeDepense);
            $typeDepense->setSyndic($this);
        }

        return $this;
    }

    public function removeTypeDepense(TypeDepense $typeDepense): static
    {
        if ($this->typeDepenses->removeElement($typeDepense)) {
            // set the owning side to null (unless already changed)
            if ($typeDepense->getSyndic() === $this) {
                $typeDepense->setSyndic(null);
            }
        }

        return $this;
    }

    public function hasBeforeDohaPeriode(): bool
    {
        return $this->nom == self::GH_16;
    }
}
