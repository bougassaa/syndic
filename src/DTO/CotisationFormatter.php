<?php

namespace App\DTO;

use App\Entity\Appartement;
use App\Entity\Cotisation;
use App\Entity\Proprietaire;
use App\Entity\Tarif;

class CotisationFormatter
{

    public ?Appartement $appartement = null;
    public ?Proprietaire $proprietaire = null;
    /** @var Cotisation[]  */
    public array $cotisations = [];
    public ?Tarif $tarif = null;

    public function getStatus(): string
    {
        if ($this->proprietaire === null) {
            return 'exempt';
        }

        $sum = 0;

        foreach ($this->cotisations as $cotisation) {
            $sum += $cotisation->getMontant();
        }

        if ($sum >= $this->tarif->getTarif()) {
            return 'paid';
        } elseif ($sum > 0) {
            return 'partial';
        } else {
            return 'overdue';
        }
    }

    public function getAppartement(): ?Appartement
    {
        return $this->appartement;
    }

    public function getProprietaire(): ?Proprietaire
    {
        return $this->proprietaire;
    }

    public function getCotisations(): array
    {
        return $this->cotisation;
    }

    public function getTarif(): ?Tarif
    {
        return $this->tarif;
    }

}