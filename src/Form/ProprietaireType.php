<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Entity\Proprietaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProprietaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('beginAt', null, [
                'widget' => 'single_text',
            ])
            ->add('leaveAt', null, [
                'widget' => 'single_text',
            ])
            ->add('appartement', EntityType::class, [
                'class' => Appartement::class,
                'choice_label' => function(Appartement $appartement) {
                    return $appartement->getBatiment()->getNom() . ' - ' . $appartement->getNumero();
                },
                'group_by' => function(Appartement $appartement) {
                    return $appartement->getBatiment()->getNom();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proprietaire::class,
        ]);
    }
}
