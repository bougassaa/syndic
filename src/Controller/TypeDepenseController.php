<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Syndic;
use App\Entity\TypeDepense;
use App\Form\TypeDepenseType;
use App\Repository\TypeDepenseRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class TypeDepenseController extends AbstractController
{
    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/type-depense', name: 'app_type_depense_list')]
    public function list(TypeDepenseRepository $repository): Response
    {
        return $this->render('type_depense/list.html.twig', [
            'typeDepenses' => $repository->getSyndicTypeDepenses($this->syndic),
        ]);
    }

    #[Route('/type-depense/new', name: 'app_type_depense_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $type = new TypeDepense();

        $form = $this->createForm(TypeDepenseType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type->setSyndic($this->syndic);
            $manager->persist($type);
            $manager->flush();

            return $this->redirectToRoute('app_type_depense_list');
        }

        return $this->render('type_depense/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/type-depense/edit/{typeDepense}', name: 'app_type_depense_edit')]
    public function edit(TypeDepense $typeDepense, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TypeDepenseType::class, $typeDepense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('app_type_depense_list');
        }

        return $this->render('type_depense/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
