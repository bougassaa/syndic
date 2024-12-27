<?php

namespace App\Entity;

use App\Repository\GarageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GarageRepository::class)]
class Garage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Syndic $syndic = null;

    #[ORM\ManyToOne(inversedBy: 'garages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Proprietaire $proprietaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateAchat = null;

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

    public function getSyndic(): ?Syndic
    {
        return $this->syndic;
    }

    public function setSyndic(?Syndic $syndic): static
    {
        $this->syndic = $syndic;

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

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): static
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }
}
