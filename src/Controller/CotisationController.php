<?php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Cotisation;
use App\Entity\Syndic;
use App\Entity\Tarif;
use App\Form\CotisationType;
use App\Repository\BatimentRepository;
use App\Repository\TarifRepository;
use App\Service\CotisationsDisplay;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class CotisationController extends AbstractController
{

    use TarifFilterSelection;

    private Syndic $syndic;

    public function __construct(
        private TarifRepository $tarifRepository,
        private BatimentRepository $batimentRepository,
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

        return $this->render('cotisation/list.html.twig', [
            'items' => $cotisationsDisplay->getCotisations($tarifSelected, $filterBatiment),
            'totalCotisations' => $cotisationsDisplay->getTotalCotisations(),
            'tarifs' => $this->tarifRepository->getSyndicTarifs($this->syndic),
            'batimentsFilter' => $this->batimentRepository->getSyndicBatiments($this->syndic),
            'tarifSelected' => $tarifSelected,
            'batimentSelected' => $filterBatiment,
            'syndic' => $this->syndic,
        ]);
    }

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
            if ($proprietaire = $cotisation->getAppartement()?->getLastProprietaire()) {
                $cotisation->setProprietaire($proprietaire);
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

            $manager->persist($cotisation);
            $manager->flush();

            return $this->redirectToRoute('app_cotisation_list');
        }

        return $this->render('cotisation/new.html.twig', [
            'form' => $form,
            'tarifsMapping' => $this->getTarifsMapping()
        ]);
    }

    #[Route('/cotisation/more-infos/{tarif}/{appartement}', name: 'app_cotisation_more_infos')]
    public function moreInfos(Tarif $tarif, Appartement $appartement): Response
    {
        $preuves = [];

        foreach ($tarif->getCotisations() as $cotisation) {
            if ($cotisation->getAppartement() === $appartement) {
                $preuves = array_merge($preuves, $cotisation->getPreuves());
            }
        }

        return $this->render('_components/preuves-modal.html.twig', [
            'preuves' => $preuves,
            'pathFolder' => 'cotisations'
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
}
