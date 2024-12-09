<?php

namespace App\Form;

use App\Entity\Cotisation;
use App\Entity\Proprietaire;
use App\Entity\Tarif;
use App\Form\Type\AppartementFieldType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ])
            ->add('moyenPaiement', null, [
                'label' => $this->translator->trans('cotisation.moyenPaiement'),
            ])
            ->add('appartement', AppartementFieldType::class)
            ->add('proprietaire', EntityType::class, [
                'class' => Proprietaire::class,
                'choice_label' => 'id',
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
