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

    public function __construct(private SyndicSessionResolver $syndicSessionResolver,
                                private TarifRepository $tarifRepository,
                                private AppartementRepository $appartementRepository,
                                private DepenseRepository $depenseRepository,
                                private CotisationRepository $cotisationRepository)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        list($currentTarif, $totalCotisations, $totalCotisationsAttendues) = $this->getCotisationsWidgetValues();
        list($paidCotisations, $pendingCotisations) = $this->getStatusCotisationsWidgetValues($currentTarif);

        return $this->render('home/index.html.twig', [
            'totalCotisationsAttendues' => $totalCotisationsAttendues,
            'totalCotisations' => $totalCotisations,
            'totalDepenses' => $this->getDepensesWidgetValues(),
            'currentTarif' => $currentTarif,
            'paidCotisations' => $paidCotisations,
            'pendingCotisations' => $pendingCotisations
        ]);
    }

    private function getCurrentTarif(): ?Tarif
    {
        $currentTarif = $this->tarifRepository->getCurrentTarif($this->syndic);

        if (!$currentTarif) { // get last tarif as fallback
            $currentTarif = $this->tarifRepository->getMaxYearTarif($this->syndic);
        }

        return $currentTarif;
    }

    private function getCotisationsWidgetValues(): array
    {
        $totalCotisations = 0;
        $totalCotisationsAttendues = 0;

        $currentTarif = $this->getCurrentTarif();

        if ($currentTarif) {
            $totalCotisations = $this->cotisationRepository->getSumOfYear($currentTarif);

            $numberOfAppartements = $this->appartementRepository->getNumberOfAppartements($this->syndic);
            $totalCotisationsAttendues = $numberOfAppartements * $currentTarif->getTarif();
        }

        return [$currentTarif, $totalCotisations, $totalCotisationsAttendues];
    }

    private function getDepensesWidgetValues(): float
    {
        $currentTarif = $this->getCurrentTarif();
        return $this->depenseRepository->getTotalDepensesPerPeriode($currentTarif, $this->syndic);
    }

    private function getStatusCotisationsWidgetValues(Tarif|null $currentTarif): array
    {
        $paidCotisations = 0;
        $pendingCotisations = 0;
        if ($currentTarif) {
            $proprietaires = []; // todo : handle it + isExempt
//            $proprietaires = $this->proprietaireRepository->getSyndicProprietaires($this->syndic);
            foreach ($proprietaires as $proprietaire) {
                if (!$proprietaire->isExempt($currentTarif)) {
                    if ($proprietaire->getCotisations()->isEmpty()) {
                        $pendingCotisations++;
                    } else {
                        $sum = 0;
                        $hasPartial = false;
                        foreach ($proprietaire->getCotisations() as $cotisation) {
                            if ($cotisation->getTarif() === $currentTarif) {
                                $sum += $cotisation->getMontant();
                                if ($cotisation->isPartial()) {
                                    $hasPartial = true;
                                }
                            }
                        }
                        if ($sum >= $currentTarif->getTarif() || $hasPartial) {
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
