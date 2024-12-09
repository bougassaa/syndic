<?php

namespace App\Form;

use App\Entity\Cotisation;
use App\Entity\Proprietaire;
use App\Entity\Tarif;
use App\Form\Type\AppartementFieldType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CotisationType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paidAt', null, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('cotisation.paidAt'),
            ])
            ->add('montant', MoneyType::class, [
                'label' => $this->translator->trans('cotisation.montant'),
                'currency' => 'MAD',
                'html5' => true,
                'attr' => [
                    'type' => 'number',
                    'step' => '0.01',
                ],
            ])
            ->add('moyenPaiement', ChoiceType::class, [
                'label' => $this->translator->trans('cotisation.moyenPaiement'),
                'choices' => array_combine(
                    array_map(fn($key) => $this->translator->trans('moyenPaiement.' . $key), Cotisation::MOYENS_PAIEMENTS),
                    Cotisation::MOYENS_PAIEMENTS
                ),
            ])
            ->add('appartement', AppartementFieldType::class, [
                'placeholder' => $this->translator->trans('select-choose'),
            ])
            ->add('proprietaire', EntityType::class, [
                'class' => Proprietaire::class,
                'choice_label' => 'id',
                'placeholder' => $this->translator->trans('select-choose'),
            ])
            ->add('tarif', EntityType::class, [
                'class' => Tarif::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('save')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cotisation::class,
        ]);
    }
}
