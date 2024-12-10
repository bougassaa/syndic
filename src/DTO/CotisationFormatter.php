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
    public array $cotisations = [];
    public ?Tarif $tarif = null;

    public function getStatus()
    {
        // success if cotisation is not null and montant equal or greater than tarif
        // partial if cotisation is not null and montant less than tarif
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
        return $this->cotisations;
    }

    public function getTarif(): ?Tarif
    {
        return $this->tarif;
    }

}