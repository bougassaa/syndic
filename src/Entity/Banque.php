<?php

namespace App\Entity;

use App\Repository\BanqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanqueRepository::class)]
class Banque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $numeroBanque = null;

    #[ORM\Column(length: 100)]
    private ?string $rib = null;

    #[ORM\Column(length: 255)]
    private ?string $labelCompte = null;

    #[ORM\Column(length: 120)]
    private ?string $agence = null;

    #[ORM\OneToOne(inversedBy: 'banque', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Syndic $syndic = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroBanque(): ?string
    {
        return $this->numeroBanque;
    }

    public function setNumeroBanque(string $numeroBanque): static
    {
        $this->numeroBanque = $numeroBanque;

        return $this;
    }

    public function getRib(): ?string
    {
        return $this->rib;
    }

    public function setRib(string $rib): static
    {
        $this->rib = $rib;

        return $this;
    }

    public function getLabelCompte(): ?string
    {
        return $this->labelCompte;
    }

    public function setLabelCompte(string $labelCompte): static
    {
        $this->labelCompte = $labelCompte;

        return $this;
    }

    public function getAgence(): ?string
    {
        return $this->agence;
    }

    public function setAgence(string $agence): static
    {
        $this->agence = $agence;

        return $this;
    }

    public function getSyndic(): ?Syndic
    {
        return $this->syndic;
    }

    public function setSyndic(Syndic $syndic): static
    {
        $this->syndic = $syndic;

        return $this;
    }
}
