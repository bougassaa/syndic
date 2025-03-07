<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Entity\Syndic;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use App\Repository\TarifRepository;
use App\Repository\TypeDepenseRepository;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DepenseController extends AbstractController
{

    use TarifFilterSelection;
    use SavePreuves;

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver,
                                private TarifRepository $tarifRepository,
                                private TypeDepenseRepository $typeDepenseRepository,
    )
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/depense', name: 'app_depense_list')]
    public function list(DepenseRepository $repository, #[MapQueryParameter] ?int $filterPeriode, #[MapQueryParameter] ?string $filterMonth): Response
    {
        $tarifSelected = $this->getSelectedTarif($filterPeriode);
        if (!$tarifSelected) {
            // todo : make template for depenses only
            return $this->render('cotisation/empty-tarif.html.twig');
        }

        $depenses = $repository->getDepensesPerPeriode($tarifSelected, $this->syndic, $filterMonth);
        $totalDepenses = $repository->getTotalDepensesPerPeriode($tarifSelected, $this->syndic, $filterMonth);

        return $this->render('depense/list.html.twig', [
            'depenses' => $depenses,
            'totalDepenses' => $totalDepenses,
            'tarifSelected' => $tarifSelected,
            'monthSelected' => $filterMonth,
            'tarifs' => $this->tarifRepository->getSyndicTarifs($this->syndic),
            'syndic' => $this->syndic,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/depense/new', name: 'app_depense_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $depense = new Depense();
        $depense->setPaidAt(new \DateTime());

        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlePreuves($form, $depense, 'depenses');

            $depense->setSyndic($this->syndic);
            $manager->persist($depense);
            $manager->flush();

            return $this->redirectToRoute('app_depense_list');
        }

        return $this->render('depense/save.html.twig', [
            'form' => $form,
            'types' => $this->getTypesDepenses(),
            'mode' => 'new'
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/depense/edit/{depense}', name: 'app_depense_edit')]
    public function edit(Depense $depense, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(DepenseType::class, $depense, [
            'existing_preuves' => $depense->getPreuves(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlePreuves($form, $depense, 'depenses');

            $manager->persist($depense);
            $manager->flush();

            return $this->redirectToRoute('app_depense_list');
        }

        return $this->render('depense/save.html.twig', [
            'form' => $form,
            'types' => $this->getTypesDepenses(),
            'mode' => 'edit'
        ]);
    }

    private function getTypesDepenses(): array
    {
        $types = [];
        foreach ($this->typeDepenseRepository->findBy(['syndic' => $this->syndic]) as $item) {
            $types[$item->getId()] = $item->getMontant();
        }
        return $types;
    }

    #[Route('/depense/more-infos/{depense}', name: 'app_depense_more_infos')]
    public function moreInfos(Depense $depense): Response
    {
        return $this->render('_components/preuves-modal.html.twig', [
            'preuves' => $depense->getPreuves(),
            'pathFolder' => 'depenses'
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/depense/delete/{depense}', name: 'app_depense_delete')]
    public function delete(Depense $depense, EntityManagerInterface $manager, Request $request): Response
    {
        $manager->remove($depense);
        $manager->flush();
        return $this->redirect(
            $request->headers->get('referer') ?? $this->generateUrl('app_depense_list')
        );
    }
}
