<?php

namespace App\Entity;

use App\Repository\TarifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TarifRepository::class)]
class Tarif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $tarif = null;

    #[ORM\ManyToOne(inversedBy: 'tarifs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Syndic $syndic = null;

    /**
     * @var Collection<int, Cotisation>
     */
    #[ORM\OneToMany(targetEntity: Cotisation::class, mappedBy: 'tarif')]
    private Collection $cotisations;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $debutPeriode = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $finPeriode = null;

    public function __construct()
    {
        $this->cotisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarif(): ?float
    {
        return $this->tarif;
    }

    public function setTarif(float $tarif): static
    {
        $this->tarif = $tarif;

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
            $cotisation->setTarif($this);
        }

        return $this;
    }

    public function removeCotisation(Cotisation $cotisation): static
    {
        if ($this->cotisations->removeElement($cotisation)) {
            // set the owning side to null (unless already changed)
            if ($cotisation->getTarif() === $this) {
                $cotisation->setTarif(null);
            }
        }

        return $this;
    }

    public function getDebutPeriode(): ?\DateTimeInterface
    {
        return $this->debutPeriode;
    }

    public function setDebutPeriode(\DateTimeInterface $debutPeriode): static
    {
        $this->debutPeriode = $debutPeriode;

        return $this;
    }

    public function getFinPeriode(): ?\DateTimeInterface
    {
        return $this->finPeriode;
    }

    public function setFinPeriode(\DateTimeInterface $finPeriode): static
    {
        $this->finPeriode = $finPeriode;

        return $this;
    }

    public function getPeriodeYear(): string
    {
        return $this->debutPeriode->format('Y') . ' - ' . $this->finPeriode->format('Y');
    }
}
