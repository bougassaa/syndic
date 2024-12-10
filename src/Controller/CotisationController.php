<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Syndic;
use App\Form\CotisationType;
use App\Repository\AppartementRepository;
use App\Repository\TarifRepository;
use App\Service\CotisationsDisplay;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CotisationController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private TarifRepository $tarifRepository, private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/cotisation', name: 'app_cotisation_list')]
    public function list(CotisationsDisplay $cotisationsDisplay): Response
    {
        $year = 2024;
        $batimentKey = null;

        return $this->render('cotisation/list.html.twig', [
            'items' => $cotisationsDisplay->getCotisations($year, $batimentKey),
        ]);
    }

    #[Route('/cotisation/new', name: 'app_cotisation_new')]
    public function new(Request $request, EntityManagerInterface $manager, AppartementRepository $appartementRepository): Response
    {
        $cotisation = new Cotisation();
        $cotisation->setPaidAt(new \DateTime());

        $yearTarif = $this->tarifRepository->getThisYearTarif($this->syndic);
        if ($yearTarif) {
            $cotisation->setMontant($yearTarif->getTarif());
            $cotisation->addTarif($yearTarif);
        }

        $form = $this->createForm(CotisationType::class, $cotisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
