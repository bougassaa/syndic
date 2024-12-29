<?php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Cotisation;
use App\Entity\Syndic;
use App\Entity\Tarif;
use App\Form\CotisationType;
use App\Repository\AppartementRepository;
use App\Repository\BatimentRepository;
use App\Repository\ProprietaireRepository;
use App\Repository\TarifRepository;
use App\Service\CotisationsDisplay;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CotisationController extends AbstractController
{

    use TarifFilterSelection;

    private Syndic $syndic;

    public function __construct(
        private TarifRepository $tarifRepository,
        private BatimentRepository $batimentRepository,
        private AppartementRepository $appartementRepository,
        private SyndicSessionResolver $syndicSessionResolver
    )
    {
        $this->syndic = $this->syndicSessionResolver->getSelectedSyndic();
    }

    #[Route('/cotisation', name: 'app_cotisation_list')]
    public function list(CotisationsDisplay $cotisationsDisplay, #[MapQueryParameter] ?int $filterPeriode, #[MapQueryParameter] ?string $filterBatiment): Response
    {
        $tarifSelected = $this->getSelectedTarif($filterPeriode);
        if (!$tarifSelected) {
            return $this->render('cotisation/empty-tarif.html.twig');
        }

        $filterBatiment = filter_var($filterBatiment, FILTER_VALIDATE_INT);

        return $this->render(
            $tarifSelected->isBeforeDohaPeriode() ?
                'cotisation/before-doha-period.html.twig' :
                'cotisation/list.html.twig',
            [
            'items' => $cotisationsDisplay->getCotisations($tarifSelected, $filterBatiment),
            'totalCotisations' => $cotisationsDisplay->getTotalCotisations(),
            'tarifs' => $this->tarifRepository->getSyndicTarifs($this->syndic),
            'batimentsFilter' => $this->batimentRepository->getSyndicBatiments($this->syndic),
            'tarifSelected' => $tarifSelected,
            'batimentSelected' => $filterBatiment,
            'syndic' => $this->syndic,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/cotisation/new', name: 'app_cotisation_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $cotisation = new Cotisation();
        $cotisation->setPaidAt(new \DateTime());

        $yearTarif = $this->tarifRepository->getCurrentTarif($this->syndic);
        if ($yearTarif) {
            $cotisation->setMontant($yearTarif->getTarif());
            $cotisation->setTarif($yearTarif);
        }

        $form = $this->createForm(CotisationType::class, $cotisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlePreuves($form, $cotisation);

            $manager->persist($cotisation);
            $manager->flush();

            return $this->redirectToRoute('app_cotisation_list');
        }

        return $this->render('cotisation/new.html.twig', [
            'form' => $form,
            'tarifsMapping' => $this->getTarifsMapping(),
            'appartementsMapping' => $this->getAppartementsMapping()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/cotisation/edit/{cotisation}', name: 'app_cotisation_edit')]
    public function edit(Cotisation $cotisation, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CotisationType::class, $cotisation, [
            'existing_preuves' => $cotisation->getPreuves(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlePreuves($form, $cotisation);

            $manager->persist($cotisation);
            $manager->flush();

            return $this->redirectToRoute('app_cotisation_list');
        }

        return $this->render('cotisation/edit.html.twig', [
            'form' => $form,
            'tarifsMapping' => $this->getTarifsMapping(),
            'appartementsMapping' => $this->getAppartementsMapping()
        ]);
    }

    private function handlePreuves(FormInterface $form, Cotisation $cotisation)
    {
        if ($form->has('existingPreuves')) {
            $existingPreuves = $form->get('existingPreuves')->getData();
            $existingPreuves = json_decode($existingPreuves, true);
            $cotisation->setPreuves($existingPreuves);
        }

        /** @var UploadedFile[] $preuves */
        $preuves = $form->get('preuves')->getData();

        if (!empty($preuves)) {
            foreach ($preuves as $file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('cotisations_preuves'), $filename);
                $cotisation->addPreuve($filename);
            }
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/cotisation/delete/{cotisation}', name: 'app_cotisation_delete')]
    public function delete(Cotisation $cotisation, EntityManagerInterface $manager, Request $request): Response
    {
        $manager->remove($cotisation);
        $manager->flush();
        return $this->redirect(
            $request->headers->get('referer') ?? $this->generateUrl('app_cotisation_list')
        );
    }

    #[Route('/cotisation/more-infos/{tarif}/{appartement}', name: 'app_cotisation_more_infos')]
    public function moreInfos(Tarif $tarif, Appartement $appartement, ProprietaireRepository $proprietaireRepository): Response
    {
        $cotisationDisplay = new CotisationsDisplay($proprietaireRepository);

        return $this->render('cotisation/show-infos-modal.html.twig', [
            'cotisationsFormatter' => $cotisationDisplay->createResultFormatter($appartement, $tarif),
        ]);
    }

    private function getTarifsMapping(): array
    {
        $mapping = [];
        $tarifs = $this->tarifRepository->getSyndicTarifs($this->syndic);

        foreach ($tarifs as $tarif) {
            $mapping[$tarif->getId()] = $tarif->getTarif();
        }

        return $mapping;
    }

    private function getAppartementsMapping(): array
    {
        $mapping = [];
        $appartements = $this->appartementRepository->getSyndicAppartements($this->syndic);

        foreach ($appartements as $appartement) {
            $proprietaires = [];
            foreach ($appartement->getPossessions() as $po) {
                if ($po->getProprietaire()) {
                    $proprietaire = $po->getProprietaire();
                    $proprietaires[$proprietaire->getId()] = [
                        'value' => $proprietaire->getId(),
                        'text' => $proprietaire->getAbsoluteName()
                    ];
                }
            }

            $mapping[$appartement->getId()] = array_values($proprietaires);
        }

        return $mapping;
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/cotisation/modal/edit/{tarif}/{appartement}', name: 'app_cotisation_modal_edit')]
    public function modalEdit(Tarif $tarif, Appartement $appartement): Response
    {
        return $this->render('cotisation/modal-edit.html.twig', [
            'cotisations' => $this->getCotisationsForModal($tarif, $appartement),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/cotisation/modal/delete/{tarif}/{appartement}', name: 'app_cotisation_modal_delete')]
    public function modalDelete(Tarif $tarif, Appartement $appartement): Response
    {
        return $this->render('cotisation/modal-delete.html.twig', [
            'cotisations' => $this->getCotisationsForModal($tarif, $appartement),
        ]);
    }

    /** @return Cotisation[] */
    private function getCotisationsForModal(Tarif $tarif, Appartement $appartement): array
    {
        $cotisations = [];
        foreach ($tarif->getCotisations() as $cotisation) {
            if ($cotisation->getAppartement() === $appartement) {
                $cotisations[] = $cotisation;
            }
        }
        return $cotisations;
    }
}
