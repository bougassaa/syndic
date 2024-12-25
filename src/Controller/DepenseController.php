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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class DepenseController extends AbstractController
{

    use TarifFilterSelection;

    private Syndic $syndic;

    public function __construct(private SyndicSessionResolver $syndicSessionResolver, private TarifRepository $tarifRepository)
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/depense', name: 'app_depense_list')]
    public function list(DepenseRepository $repository, #[MapQueryParameter] ?int $filterPeriode): Response
    {
        $tarifSelected = $this->getSelectedTarif($filterPeriode);
        if (!$tarifSelected) {
            // todo : make template for depenses only
            return $this->render('cotisation/empty-tarif.html.twig');
        }

        $depenses = $repository->getDepensesPerPeriode($tarifSelected, $this->syndic);

        $total = array_reduce($depenses, function ($carry, Depense $depense) {
            return $carry + (float) $depense->getMontant();
        }, 0);

        return $this->render('depense/list.html.twig', [
            'depenses' => $depenses,
            'totalDepenses' => $total,
            'tarifSelected' => $tarifSelected,
            'tarifs' => $this->tarifRepository->getSyndicTarifs($this->syndic),
        ]);
    }

    #[Route('/depense/new', name: 'app_depense_new')]
    public function new(Request $request, EntityManagerInterface $manager, TypeDepenseRepository $typeDepenseRepository): Response
    {
        $depense = new Depense();
        $depense->setPaidAt(new \DateTime());

        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $preuves */
            $preuves = $form->get('preuves')->getData();

            if (!empty($preuves)) {
                foreach ($preuves as $file) {
                    $filename = uniqid() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('depenses_preuves'), $filename);
                    $depense->addPreuve($filename);
                }
            }

            $depense->setSyndic($this->syndic);
            $manager->persist($depense);
            $manager->flush();

            return $this->redirectToRoute('app_depense_list');
        }

        $types = [];
        foreach ($typeDepenseRepository->findBy(['syndic' => $this->syndic]) as $item) {
            $types[$item->getId()] = $item->getMontant();
        }

        return $this->render('depense/new.html.twig', [
            'form' => $form,
            'types' => $types
        ]);
    }

    #[Route('/depense/more-infos/{depense}', name: 'app_depense_more_infos')]
    public function moreInfos(Depense $depense): Response
    {
        return $this->render('_components/preuves-modal.html.twig', [
            'preuves' => $depense->getPreuves(),
            'pathFolder' => 'depenses'
        ]);
    }
}
