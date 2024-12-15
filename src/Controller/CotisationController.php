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
    public function list(CotisationsDisplay $cotisationsDisplay, #[MapQueryParameter] ?int $filterTarif, #[MapQueryParameter] ?int $filterBatiment): Response
    {
        if (!$filterTarif){
            $tarifSelected = $this->tarifRepository->getCurrentTarif($this->syndic);
            if (!$tarifSelected) {
                $tarifSelected = $this->tarifRepository->getMaxYearTarif($this->syndic);
                if (!$tarifSelected) {
                    return $this->render('cotisation/empty-tarif.html.twig');
                }
            }
        } else {
            $tarifSelected = $this->tarifRepository->find($filterTarif);
        }

        $filterBatiment = $filterBatiment === 0 ? null : $filterBatiment;

        return $this->render('cotisation/list.html.twig', [
            'items' => $cotisationsDisplay->getCotisations($tarifSelected, $filterBatiment),
            'totalCotisations' => $cotisationsDisplay->getTotalCotisations(),
            'tarifs' => $this->tarifRepository->getSyndicTarifs($this->syndic),
            'batimentsFilter' => $this->batimentRepository->getSyndicBatiments($this->syndic),
            'tarifSelected' => $tarifSelected,
            'batimentSelected' => $filterBatiment
        ]);
    }

    #[Route('/cotisation/new', name: 'app_cotisation_new')]
    public function new(Request $request, EntityManagerInterface $manager, AppartementRepository $appartementRepository): Response
    {
        $cotisation = new Cotisation();
        $cotisation->setPaidAt(new \DateTime());

        $yearTarif = $this->tarifRepository->getCurrentTarif($this->syndic);
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
            'tarifsMapping' => $this->getTarifsMapping()
        ]);
    }

    private function getTarifsMapping(): array
    {
        $mapping = [];
        $tarifs = $this->tarifRepository->getSyndicTarifs($this->syndic);

        foreach ($tarifs as $tarif) {
            $mapping[$tarif->getId()] = $tarif->getTarif();
        }

        return $mapping;
    }
}
