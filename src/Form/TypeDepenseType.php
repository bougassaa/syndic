<?php

namespace App\Form;

use App\Entity\Syndic;
use App\Entity\TypeDepense;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class TypeDepenseType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', null, [
                'label' => $this->translator->trans('type-depense.label'),
                'help' => $this->translator->trans('type-depense.label-help')
            ])
            ->add('frequence', ChoiceType::class, [
                'label' => $this->translator->trans('type-depense.frequence'),
                'help' => $this->translator->trans('type-depense.frequence-help'),
                'placeholder' => $this->translator->trans('select-choose'),
                'choices' => [
                    $this->translator->trans('type-depense.mensuelle') => TypeDepense::MENSUELLE,
                    $this->translator->trans('type-depense.annuelle') => TypeDepense::ANNUELLE,
                    $this->translator->trans('type-depense.occasionnelle') => TypeDepense::OCCASIONNELLE,
                ]
            ])
            ->add('montant', MoneyType::class, [
                'label' => $this->translator->trans('type-depense.montant'),
                'help' => $this->translator->trans('type-depense.montant-help'),
                'required' => false,
                'currency' => 'MAD',
                'html5' => true,
                'attr' => [
                    'type' => 'number',
                    'step' => '0.01',
                    'placeholder' => $this->translator->trans('optional')
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
            'data_class' => TypeDepense::class,
        ]);
    }
}
