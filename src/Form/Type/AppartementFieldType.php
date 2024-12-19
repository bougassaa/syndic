<?php

namespace App\Form\Type;

use App\Entity\Appartement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppartementFieldType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Appartement::class,
            'choice_label' => function (Appartement $appartement) {
                return $appartement->getAbsoluteName(false);
            },
            'group_by' => function (Appartement $appartement) {
                return $appartement->getBatiment()->getNom();
            },
            'label' => $this->translator->trans('appartement.nom'),
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

}