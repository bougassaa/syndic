<?php

namespace App\Service;

use App\DTO\CotisationFormatter;
use App\Entity\Appartement;
use App\Entity\Tarif;
use App\Repository\BatimentRepository;
use App\Repository\ProprietaireRepository;

class CotisationsDisplay
{

    private ?float $totalCotisations = null;

    public function __construct(
        private ProprietaireRepository $proprietaireRepository,
        private ?BatimentRepository    $batimentRepository = null,
        private ?SyndicSessionResolver $syndicSessionResolver = null
    )
    {
    }

    /** @return CotisationFormatter[] */
    public function getCotisations(Tarif $tarif, int|false $batimentKey): array
    {
        $cotisationsDisplay = [];
        // get selected syndic
        $syndic = $this->syndicSessionResolver->getSelectedSyndic();
        // get batiments of this syndic
        $batiments = $this->batimentRepository->findBy(['syndic' => $syndic], ['nom' => 'ASC']);
        // init total cotisations
        $this->totalCotisations = 0;

        foreach ($batiments as $batiment) {
            if ($batiment->getId() == $batimentKey || $batimentKey === false) {
                // get appartements of current batiment
                $appartements = $batiment->getAppartements();
                foreach ($appartements as $appartement) {
                    // add to results
                    $cotisationsDisplay[] = $this->createResultFormatter($appartement, $tarif);
                }
            }
        }
        return $cotisationsDisplay;
    }

    public function getTotalCotisations(): float
    {
        if (is_null($this->totalCotisations)) {
            throw new \Exception('call getCotisations first');
        }
        return $this->totalCotisations;
    }

    public function createResultFormatter(Appartement $appartement, Tarif $tarif): CotisationFormatter
    {
        $formatter = new CotisationFormatter();
        $formatter->appartement = $appartement;
        $formatter->tarif = $tarif;
        // get cotisations of current appartement
        $cotisations = $appartement->getCotisations();
        foreach ($cotisations as $cotisation) {
            // compare if concerning year
            if ($cotisation->getTarif() === $tarif) {
                $formatter->cotisations[] = $cotisation;
                $formatter->proprietaire = $cotisation->getProprietaire();
                $this->totalCotisations += $cotisation->getMontant();
            }
        }

        if (empty($formatter->proprietaire)) {
            $proprietaire = $this->proprietaireRepository->getProprietaireInTarif($tarif, $appartement);

            if (!$proprietaire) {
                $proprietaire = $appartement->getLastProprietaire();
            }

            $formatter->proprietaire = $proprietaire;
        }
        return $formatter;
    }

}