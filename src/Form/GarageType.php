<?php

namespace App\Form;

use App\Entity\Garage;
use App\Form\Type\ProprietaireFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class GarageType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => $this->translator->trans('garage.nom'),
            ])
            ->add('dateAchat', null, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('garage.dateAchat'),
            ])
            ->add('proprietaire', ProprietaireFieldType::class)
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('save')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Garage::class,
        ]);
    }
}
