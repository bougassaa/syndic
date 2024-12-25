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

        if ($sum >= $this->tarif->getTarif() || $this->hasPartialPayment()) {
            return 'paid';
        } elseif ($sum > 0) {
            return 'incomplete';
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
        return $this->cotisations;
    }

    public function getTarif(): ?Tarif
    {
        return $this->tarif;
    }

    public function hasPartialPayment(): bool
    {
        foreach ($this->cotisations as $cotisation) {
            if ($cotisation->isPartial()) {
                return true;
            }
        }
        return false;
    }

    public function getPartialReason(): string
    {
        foreach ($this->cotisations as $cotisation) {
            if ($cotisation->isPartial()) {
                return $cotisation->getPartialReason();
            }
        }
        return '';
    }

    public function hasPreuves(): bool
    {
        foreach ($this->cotisations as $cotisation) {
            if (!empty($cotisation->getPreuves())) {
                return true;
            }
        }
        return false;
    }

}