<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Banque;
use App\Entity\Syndic;
use App\Form\BanqueType;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class BanqueController extends AbstractController
{
    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/banque/edit', name: 'app_banque_save')]
    public function save(Request $request, EntityManagerInterface $manager): Response
    {
        $banque = $this->getDefaultBanque();
        $form = $this->createForm(BanqueType::class, $banque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $banque->setSyndic($this->syndic);
            $manager->persist($banque);
            $manager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('banque/save.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/banque/modal', name: 'app_show_rib_modal')]
    public function showRIBModal(): Response
    {
        return $this->render('banque/rib-modal.html.twig', [
            'banque' => $this->getDefaultBanque(),
        ]);
    }

    private function getDefaultBanque(): Banque
    {
        if ($banque = $this->syndic->getBanque()) {
            return $banque;
        }
        return new Banque();
    }
}
