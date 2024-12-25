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
            // gérer le cas avant 2022
            $endPeriod = new \DateTime('2022-07-31');
            if ($minYearTarif = $this->tarifRepository->getMinYearTarif($this->syndic)) {
                $endPeriod = $minYearTarif->getDebutPeriode();
            }
            $tarifSelected = new Tarif();
            $tarifSelected->setBeforeDohaPeriode(true);
            $tarifSelected->setDebutPeriode(new \DateTime('2014-01-01'));
            $tarifSelected->setFinPeriode($endPeriod);
        } else {
            $tarifSelected = $this->tarifRepository->find($filterPeriode);
        }

        return $tarifSelected;
    }

}