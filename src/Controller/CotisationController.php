<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Syndic;
use App\Form\CotisationType;
use App\Repository\AppartementRepository;
use App\Repository\BatimentRepository;
use App\Repository\TarifRepository;
use App\Service\CotisationsDisplay;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class CotisationController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(
        private TarifRepository $tarifRepository,
        private BatimentRepository $batimentRepository,
        private SyndicSessionResolver $syndicSessionResolver
    )
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/cotisation', name: 'app_cotisation_list')]
    public function list(CotisationsDisplay $cotisationsDisplay, #[MapQueryParameter] ?int $filterYear, #[MapQueryParameter] ?int $filterBatiment): Response
    {
        if (!$filterYear){
            $currentTarif = $this->tarifRepository->getMaxYearTarif($this->syndic);
            $filterYear = $currentTarif?->getYear();
        }

        $filterBatiment = $filterBatiment === 0 ? null : $filterBatiment;

        return $this->render('cotisation/list.html.twig', [
            'items' => $cotisationsDisplay->getCotisations($filterYear, $filterBatiment),
            'totalCotisations' => $cotisationsDisplay->getTotalCotisations(),
            'yearsFilter' => $this->tarifRepository->getYearsTarifs($this->syndic),
            'batimentsFilter' => $this->batimentRepository->getSyndicBatiments($this->syndic),
            'yearSelected' => $filterYear,
            'batimentSelected' => $filterBatiment
        ]);
    }

    #[Route('/cotisation/new', name: 'app_cotisation_new')]
    public function new(Request $request, EntityManagerInterface $manager, AppartementRepository $appartementRepository): Response
    {
        $cotisation = new Cotisation();
        $cotisation->setPaidAt(new \DateTime());

        $yearTarif = $this->tarifRepository->getMaxYearTarif($this->syndic);
        if ($yearTarif) {
            $cotisation->setMontant($yearTarif->getTarif());
            $cotisation->setTarif($yearTarif);
        }

        $form = $this->createForm(CotisationType::class, $cotisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($appartement = $cotisation->getProprietaire()?->getAppartement()) {
                $cotisation->setAppartement($appartement);
            }

            $manager->persist($cotisation);
            $manager->flush();

            return $this->redirectToRoute('app_cotisation_list');
        }

        return $this->render('cotisation/new.html.twig', [
            'form' => $form,
            'appartementsMapping' => $appartementRepository->getAppartementsProprietaires()
        ]);
    }
}
