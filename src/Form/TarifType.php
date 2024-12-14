<?php

namespace App\Form;

use App\Entity\Tarif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class TarifType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('debutPeriode', null, [
                'label' => $this->translator->trans('tarif.debutPeriode'),
            ])
            ->add('finPeriode', null, [
                'label' => $this->translator->trans('tarif.finPeriode'),
                'help' => $this->translator->trans('tarif.finPeriode-help'),
                'attr' => [
                    'readonly' => true
                ],
            ])
            ->add('tarif', MoneyType::class, [
                'label' => $this->translator->trans('tarif.montant'),
                'currency' => 'MAD',
                'html5' => true,
                'attr' => [
                    'type' => 'number',
                    'step' => '0.01',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('save')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tarif::class,
        ]);
    }
}
