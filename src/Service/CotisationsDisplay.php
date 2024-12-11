<?php

namespace App\Service;

use App\DTO\CotisationFormatter;
use App\Repository\BatimentRepository;
use App\Repository\TarifRepository;

class CotisationsDisplay
{

    private ?int $totalCotisations = null;

    public function __construct(
        private BatimentRepository $batimentRepository,
        private TarifRepository $tarifRepository,
        private SyndicSessionResolver $syndicSessionResolver
    )
    {
    }

    /** @return CotisationFormatter[] */
    public function getCotisations(int $year, int|null $batimentKey): array
    {
        $cotisationsDisplay = [];
        // get selected syndic
        $syndic = $this->syndicSessionResolver->getSelectedSyndic();
        // get batiments of this syndic
        $batiments = $this->batimentRepository->findBy(['syndic' => $syndic], ['nom' => 'ASC']);
        // get batiments of this syndic
        $tarif = $this->tarifRepository->getYearTarif($syndic, $year);
        // init total cotisations
        $this->totalCotisations = 0;

        foreach ($batiments as $batiment) {
            if ($batiment->getId() == $batimentKey || is_null($batimentKey)) {
                // get appartements of current batiment
                $appartements = $batiment->getAppartements();
                foreach ($appartements as $appartement) {
                    // create result formatter
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
                        // set default proprietaire based on year and begin date
                        foreach ($appartement->getProprietaires() as $proprietaire) {
                            $beginYear = (int) $proprietaire->getBeginAt()->format('y');
                            if ($year >= $beginYear) {
                                $formatter->proprietaire = $proprietaire;
                            }
                        }
                    }

                    // add to results
                    $cotisationsDisplay[] = $formatter;
                }
            }
        }
        return $cotisationsDisplay;
    }

    public function getTotalCotisations(): int
    {
        if (is_null($this->totalCotisations)) {
            throw new \Exception('call getCotisations first');
        }
        return $this->totalCotisations;
    }

}