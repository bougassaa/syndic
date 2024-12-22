<?php

namespace App\Form;

use App\Entity\Proprietaire;
use App\Form\Type\AppartementFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProprietaireType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => $this->translator->trans('proprietaire.nom'),
                'attr' => ['class' => 'text-uppercase'],
            ])
            ->add('prenom', null, [
                'label' => $this->translator->trans('proprietaire.prenom'),
            ])
            ->add('appartement', AppartementFieldType::class, [
                'mapped' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('save')
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