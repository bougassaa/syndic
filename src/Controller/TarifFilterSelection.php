<?php

namespace App\Controller;

use App\Entity\Tarif;

trait TarifFilterSelection
{

    private function getSelectedTarif(?int $filterPeriode): ?Tarif
    {
        if (!$filterPeriode){
            $tarifSelected = $this->tarifRepository->getCurrentTarif($this->syndic);
            if (!$tarifSelected) {
                $tarifSelected = $this->tarifRepository->getMaxYearTarif($this->syndic);
            }
        } else if($filterPeriode === -1) {
            $minYearTarif = $this->tarifRepository->getMinYearTarif($this->syndic);
            // gérer le cas avant 2022
            $tarifSelected = new Tarif();
            $tarifSelected->setBeforeDohaPeriode(true);
            $tarifSelected->setDebutPeriode(new \DateTime('2014-01-01'));
            $tarifSelected->setFinPeriode($minYearTarif->getDebutPeriode());
        } else {
            $tarifSelected = $this->tarifRepository->find($filterPeriode);
        }

        return $tarifSelected;
    }

}