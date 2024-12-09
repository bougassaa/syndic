<?php

namespace App\Controller;

use App\Entity\Proprietaire;
use App\Form\ProprietaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProprietaireController extends AbstractController
{

    #[Route('/proprietaire', name: 'app_proprietaire_list')]
    public function list(EntityManagerInterface $manager): Response
    {
        $proprietaires = $manager->getRepository(Proprietaire::class)->findAll();

        return $this->render('proprietaire/list.html.twig', [
            'proprietaires' => $proprietaires,
        ]);
    }

    #[Route('/proprietaire/new', name: 'app_proprietaire_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $proprietaire = new Proprietaire();

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
}
