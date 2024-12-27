<?php

namespace App\Controller;

use App\Entity\Garage;
use App\Entity\Syndic;
use App\Form\GarageType;
use App\Repository\GarageRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GarageController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }


    #[Route('/garage', name: 'app_garage_list')]
    public function list(GarageRepository $repository): Response
    {
        return $this->render('garage/list.html.twig', [
            'garages' => $repository->getSyndicGarages($this->syndic),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/garage/new', name: 'app_garage_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $garage = new Garage();
        $garage->setSyndic($this->syndic);

        $form = $this->createForm(GarageType::class, $garage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($garage);
            $manager->flush();

            return $this->redirectToRoute('app_garage_list');
        }

        return $this->render('garage/new.html.twig', [
            'form' => $form,
        ]);
    }
}
