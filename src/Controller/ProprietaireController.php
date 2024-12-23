<?php

namespace App\Controller;

use App\Entity\Possession;
use App\Entity\Proprietaire;
use App\Entity\Syndic;
use App\Form\ProprietaireType;
use App\Repository\ProprietaireRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProprietaireController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/proprietaire', name: 'app_proprietaire_list')]
    public function list(ProprietaireRepository $repository): Response
    {
        return $this->render('proprietaire/list.html.twig', [
            'proprietaires' => $repository->getSyndicProprietaires($this->syndic),
        ]);
    }

    #[Route('/proprietaire/new', name: 'app_proprietaire_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $proprietaire = new Proprietaire();
        $proprietaire->addPossession(new Possession());

        $form = $this->createForm(ProprietaireType::class, $proprietaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($proprietaire);
            $manager->flush();

            return $this->redirectToRoute('app_proprietaire_list');
        }

        return $this->render('proprietaire/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/proprietaire/edit/{proprietaire}', name: 'app_proprietaire_edit')]
    public function edit(Proprietaire $proprietaire, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProprietaireType::class, $proprietaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('app_proprietaire_list');
        }

        return $this->render('proprietaire/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
