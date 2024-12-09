<?php

namespace App\Entity;

use App\Repository\CotisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CotisationRepository::class)]
class Cotisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'cotisations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Appartement $appartement = null;

    #[ORM\ManyToOne(inversedBy: 'cotisations')]
    private ?Proprietaire $proprietaire = null;

    /**
     * @var Collection<int, Tarif>
     */
    #[ORM\ManyToMany(targetEntity: Tarif::class, inversedBy: 'cotisations')]
    private Collection $tarif;

    public function __construct()
    {
        $this->tarif = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeInterface $paidAt): static
    {
        $this->paidAt = $paidAt;

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

    public function getAppartement(): ?Appartement
    {
        return $this->appartement;
    }

    public function setAppartement(?Appartement $appartement): static
    {
        $this->appartement = $appartement;

        return $this;
    }

    public function getProprietaire(): ?Proprietaire
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?Proprietaire $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * @return Collection<int, Tarif>
     */
    public function getTarif(): Collection
    {
        return $this->tarif;
    }

    public function addTarif(Tarif $tarif): static
    {
        if (!$this->tarif->contains($tarif)) {
            $this->tarif->add($tarif);
        }

        return $this;
    }

    public function removeTarif(Tarif $tarif): static
    {
        $this->tarif->removeElement($tarif);

        return $this;
    }
}
