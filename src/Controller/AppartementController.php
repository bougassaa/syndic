<?php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Syndic;
use App\Form\AppartementType;
use App\Repository\AppartementRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppartementController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }


    #[Route('/appartement', name: 'app_appartement_list')]
    public function list(AppartementRepository $repository): Response
    {
        return $this->render('appartement/list.html.twig', [
            'appartements' => $repository->getSyndicAppartements($this->syndic),
        ]);
    }

    #[Route('/appartement/new', name: 'app_appartement_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $appartement = new Appartement();

        $form = $this->createForm(AppartementType::class, $appartement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($appartement);
            $manager->flush();

            return $this->redirectToRoute('app_appartement_list');
        }

        return $this->render('appartement/new.html.twig', [
            'form' => $form,
        ]);
    }
}
