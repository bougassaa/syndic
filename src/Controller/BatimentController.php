<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Entity\Syndic;
use App\Form\BatimentType;
use App\Repository\BatimentRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BatimentController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/batiment', name: 'app_batiment_list')]
    public function list(BatimentRepository $repository): Response
    {
        return $this->render('batiment/list.html.twig', [
            'batiments' => $repository->getSyndicBatiments($this->syndic),
        ]);
    }

    #[Route('/batiment/new', name: 'app_batiment_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $batiment = new Batiment();
        $batiment->setSyndic($this->syndic);

        $form = $this->createForm(BatimentType::class, $batiment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($batiment);
            $manager->flush();

            return $this->redirectToRoute('app_batiment_list');
        }

        return $this->render('batiment/new.html.twig', [
            'form' => $form,
        ]);
    }
}
