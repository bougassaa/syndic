<?php

namespace App\Controller;

use App\Entity\Syndic;
use App\Entity\Tarif;
use App\Form\TarifType;
use App\Repository\TarifRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TarifController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver, private TarifRepository $tarifRepository)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/tarif/new', name: 'app_tarif_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $tarif = new Tarif();
        $lastYear = $this->tarifRepository->getMaxYearTarif($this->syndic);
        $tarif->setYear($lastYear->getYear() + 1);
        $tarif->setTarif($lastYear->getTarif());
        $tarif->setSyndic($this->syndic);

        $form = $this->createForm(TarifType::class, $tarif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($tarif);
            $manager->flush();

            return $this->redirectToRoute('app_cotisation_list');
        }

        return $this->render('tarif/new.html.twig', [
            'form' => $form
        ]);
    }
}
