<?php

namespace App\Controller;

use App\Entity\Proprietaire;
use App\Form\ProprietaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProprietaireController extends AbstractController
{
    #[Route('/proprietaire/new', name: 'app_proprietaire_new')]
    public function new(): Response
    {
        $proprietaire = new Proprietaire();

        $form = $this->createForm(ProprietaireType::class, $proprietaire);

        return $this->render('proprietaire/new.html.twig', [
            'form' => $form,
        ]);
    }
}
