<?php

namespace App\Controller;

use App\Entity\Syndic;
use App\Repository\CotisationRepository;
use App\Repository\ProprietaireRepository;
use App\Repository\TarifRepository;
use App\Service\SyndicSessionResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/', name: 'app_home')]
    public function index(TarifRepository $tarifRepository,
                          ProprietaireRepository $proprietaireRepository,
                          CotisationRepository $cotisationRepository): Response
    {
        $totalCotisations = 0;
        $totalCotisationsAttendues = 0;

        $currentTarif = $tarifRepository->getCurrentTarif($this->syndic);
        if ($currentTarif) {
            $totalCotisations = $cotisationRepository->getSumOfYear($currentTarif);

            $numberOfProprietaires = $proprietaireRepository->getNumberOfProprietaire($this->syndic);
            $totalCotisationsAttendues = $numberOfProprietaires * $currentTarif->getTarif();
        }

        return $this->render('home/index.html.twig', [
            'totalCotisationsAttendues' => $totalCotisationsAttendues,
            'totalCotisations' => $totalCotisations,
            'currentTarif' => $currentTarif,
        ]);
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
