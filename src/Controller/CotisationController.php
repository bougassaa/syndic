<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Syndic;
use App\Form\CotisationType;
use App\Repository\TarifRepository;
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

    #[Route('/cotisation/new', name: 'app_cotisation_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $cotisation = new Cotisation();
        $cotisation->setPaidAt(new \DateTime());

        $yearTarif = $this->tarifRepository->getThisYearTarif($this->syndic);
        $cotisation->setMontant($yearTarif);

        $form = $this->createForm(CotisationType::class, $cotisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($cotisation);
        }

        return $this->render('cotisation/new.html.twig', [
            'form' => $form,
        ]);
    }
}
