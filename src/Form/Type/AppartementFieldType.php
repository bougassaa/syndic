<?php

namespace App\Form\Type;

use App\Entity\Appartement;
use App\Entity\Proprietaire;
use App\Entity\Syndic;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppartementFieldType extends AbstractType
{
    private Syndic $syndic;

    public function __construct(private TranslatorInterface $translator, SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $syndicSessionResolver->getSelectedSyndic();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Appartement::class,
            'placeholder' => $this->translator->trans('select-choose'),
            'choice_label' => function (Appartement $appartement) {
                return $appartement->getAbsoluteName(false);
            },
            'group_by' => function (Appartement $appartement) {
                return $appartement->getBatiment()->getNom();
            },
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('a')
                    ->join('a.batiment', 'b')
                    ->where('b.syndic = :syndic')
                    ->setParameter('syndic', $this->syndic)
                    ->orderBy('b.nom', 'ASC')
                    ->addOrderBy('a.numero', 'ASC');
            },
            'label' => $this->translator->trans('appartement.nom'),
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

}