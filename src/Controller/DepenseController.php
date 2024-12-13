<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Entity\Syndic;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DepenseController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/depense', name: 'app_depense_list')]
    public function list(DepenseRepository $repository): Response
    {
        return $this->render('depense/list.html.twig', [
            'depenses' => $repository->findBy([], ['paidAt' => 'DESC']),
        ]);
    }

    #[Route('/depense/new', name: 'app_depense_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $depense = new Depense();
        $depense->setPaidAt(new \DateTime());

        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depense->setSyndic($this->syndic);
            $manager->persist($depense);
            $manager->flush();

            return $this->redirectToRoute('app_depense_list');
        }

        return $this->render('depense/new.html.twig', [
            'form' => $form
        ]);
    }
}
