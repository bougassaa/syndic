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
        } else {
            $tarifSelected = $this->tarifRepository->find($filterPeriode);
        }

        return $tarifSelected;
    }

}