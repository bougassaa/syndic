<?php

namespace App\Form\Type;

use App\Entity\Proprietaire;
use App\Entity\Syndic;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProprietaireFieldType extends AbstractType
{
    private Syndic $syndic;

    public function __construct(private TranslatorInterface $translator, SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $syndicSessionResolver->getSelectedSyndic();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Proprietaire::class,
            'placeholder' => $this->translator->trans('select-choose'),
            'choice_label' => function (Proprietaire $proprietaire) {
                return $proprietaire->getAbsoluteName();
            },
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->join('p.possessions', 'po')
                    ->join('po.appartement', 'a')
                    ->join('a.batiment', 'b')
                    ->where('b.syndic = :syndic')
                    ->setParameter('syndic', $this->syndic)
                    ->orderBy('p.isSystem', 'ASC')
                    ->addOrderBy('CASE WHEN po.leaveAt IS NOT NULL THEN 1 ELSE 0 END', 'ASC');
            },
            'label' => $this->translator->trans('cotisation.proprietaire'),
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

}