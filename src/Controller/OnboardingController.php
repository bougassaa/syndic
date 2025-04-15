<?php

namespace App\Controller;

use App\Entity\Syndic;
use App\Form\OnboardingType;
use App\Repository\AppartementRepository;
use App\Repository\BatimentRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OnboardingController extends AbstractController
{

    private Syndic $syndic;

    public function __construct(private readonly SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/onboarding/1', name: 'app_onboarding_step1')]
    public function step1(Request $request): Response
    {
        $referer = $request->headers->get('referer');
        $route = $this->generateUrl('app_onboarding_step1');
        if (str_ends_with($referer, $route)) {
            return $this->redirectToRoute('app_onboarding_step2');
        }
        return $this->render('onboarding/step1.html.twig');
    }

    #[Route('/onboarding/2', name: 'app_onboarding_step2')]
    public function step2(BatimentRepository $batimentRepository): Response
    {
        return $this->render('onboarding/step2.html.twig', [
            'batiments' => $batimentRepository->getSyndicBatiments($this->syndic),
        ]);
    }

    #[Route('/onboarding/3', name: 'app_onboarding_step3')]
    public function step3(Request $request, AppartementRepository $appartementRepository, EntityManagerInterface $manager): Response
    {
        $form = null;
        $param = $request->get('appartement');

        if (empty($param)) {
            return $this->redirectToRoute('app_onboarding_step2');
        }

        $appart = $appartementRepository->find($param);
        if ($appart) {
            $prop = $appart->getLastProprietaire();
            $possession = $appart->getLastPossession();
            $form = $this->createForm(OnboardingType::class, $prop, ['possession' => $possession]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $prop->setOnboardingDoneAt(new \DateTimeImmutable());

                $beginAt = $form->get('beginAt')->getData();
                if ($possession && $beginAt) {
                    $possession->setBeginAt($beginAt);
                }

                $manager->persist($possession);
                $manager->persist($prop);
                $manager->flush();

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('onboarding/step3.html.twig', [
            'propForm' => $form,
            'appart' => $appart
        ]);
    }
}
