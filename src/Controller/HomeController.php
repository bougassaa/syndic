<?php

namespace App\Controller;

use App\Entity\Syndic;
use App\Entity\Tarif;
use App\Repository\AppartementRepository;
use App\Repository\CotisationRepository;
use App\Repository\DepenseRepository;
use App\Repository\TarifRepository;
use App\Service\SyndicSessionResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    private Syndic $syndic;
    private ?Tarif $currentTarif;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver,
                                private TarifRepository $tarifRepository,
                                private AppartementRepository $appartementRepository,
                                private DepenseRepository $depenseRepository,
                                private CotisationRepository $cotisationRepository)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
        $this->currentTarif = $this->getCurrentTarif();
    }

    private function getCurrentTarif(): ?Tarif
    {
        $currentTarif = $this->tarifRepository->getCurrentTarif($this->syndic);

        if (!$currentTarif) { // get last tarif as fallback
            $currentTarif = $this->tarifRepository->getMaxYearTarif($this->syndic);
        }

        return $currentTarif;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        list($totalCotisations, $totalCotisationsAttendues) = $this->getCotisationsWidgetValues();
        list($paidCotisations, $pendingCotisations) = $this->getStatusCotisationsWidgetValues();

        return $this->render('home/index.html.twig', [
            'totalCotisationsAttendues' => $totalCotisationsAttendues,
            'totalCotisations' => $totalCotisations,
            'totalDepenses' => $this->getDepensesWidgetValues(),
            'currentTarif' => $this->currentTarif,
            'paidCotisations' => $paidCotisations,
            'pendingCotisations' => $pendingCotisations
        ]);
    }

    private function getCotisationsWidgetValues(): array
    {
        $totalCotisations = 0;
        $totalCotisationsAttendues = 0;

        if ($this->currentTarif) {
            $totalCotisations = $this->cotisationRepository->getSumOfYear($this->currentTarif);

            $numberOfAppartements = $this->getNumberOfAppartements();
            $totalCotisationsAttendues = $numberOfAppartements * $this->currentTarif->getTarif();
        }

        return [$totalCotisations, $totalCotisationsAttendues];
    }

    private function getNumberOfAppartements(): int
    {
        $number = 0;
        $appartements = $this->appartementRepository->getSyndicAppartements($this->syndic);
        foreach ($appartements as $appartement) {
            if (!$appartement->getPossessions()->isEmpty()) {
                $number++;
            }
        }

        return $number;
    }

    private function getDepensesWidgetValues(): float
    {
        return $this->depenseRepository->getTotalDepensesPerPeriode($this->currentTarif, $this->syndic);
    }

    private function getStatusCotisationsWidgetValues(): array
    {
        $paidCotisations = 0;
        $pendingCotisations = 0;

        if ($this->currentTarif) {
            $appartements = $this->appartementRepository->getSyndicAppartements($this->syndic);
            foreach ($appartements as $appartement) {
                if (!$appartement->getPossessions()->isEmpty()) {
                    if ($appartement->getCotisations()->isEmpty()) {
                        $pendingCotisations++;
                    } else {
                        $sum = 0;
                        $hasPartial = false;
                        foreach ($appartement->getCotisations() as $cotisation) {
                            if ($cotisation->getTarif() === $this->currentTarif) {
                                $sum += $cotisation->getMontant();
                                if ($cotisation->isPartial()) {
                                    $hasPartial = true;
                                }
                            }
                        }
                        if ($sum >= $this->currentTarif->getTarif() || $hasPartial) {
                            $paidCotisations++;
                        } else {
                            $pendingCotisations++;
                        }
                    }
                }
            }
        }

        return [$paidCotisations, $pendingCotisations];
    }

    #[Route('/show-rib', name: 'app_show_rib_modal')]
    public function showRIBModal(): Response
    {
        return $this->render('home/rib-modal.html.twig');
    }

    #[Route('/serve-image/{folder}/{filename}', name: 'app_serve_image')]
    public function serveImage(string $folder, string $filename): Response
    {
        $filePath = $this->getParameter($folder . '_preuves') . '/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found.');
        }

        $file = new File($filePath);

        return $this->file($file);
    }
}
