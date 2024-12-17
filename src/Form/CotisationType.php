<?php

namespace App\Form;

use App\Entity\Cotisation;
use App\Entity\Proprietaire;
use App\Entity\Tarif;
use App\Form\Type\PreuvesType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('tarif', EntityType::class, [
                'class' => Tarif::class,
                'choice_label' => fn(Tarif $tarif) => $tarif->getPeriodeYear(),
                'label' => $this->translator->trans('cotisation.tarif'),
                'attr' => ['class' => 'cotisationTarif'],
            ])
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
            ->add('isPartial', CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans('cotisation.isPartialPayment'),
                'label_attr' => ['class' => 'checkbox-switch'],
                'row_attr' => ['id' => 'isPartialPayment-row'],
                'attr' => ['class' => 'isPartialPayment'],
                'help' => $this->translator->trans('cotisation.isPartialPayment-help')
            ])
            ->add('partialReason', TextareaType::class, [
                'required' => false,
                'label' => $this->translator->trans('cotisation.partialReason'),
                'row_attr' => ['id' => 'partialPaymentReason-row'],
                'attr' => [
                    'rows' => 2,
                    'placeholder' => $this->translator->trans('cotisation.partialReason-placeholder'),
                ],
            ])
            ->add('moyenPaiement', ChoiceType::class, [
                'label' => $this->translator->trans('cotisation.moyenPaiement'),
                'choices' => array_combine(
                    array_map(fn($key) => $this->translator->trans('moyenPaiement.' . $key), Cotisation::MOYENS_PAIEMENTS),
                    Cotisation::MOYENS_PAIEMENTS
                ),
            ])
            ->add('proprietaire', EntityType::class, [
                'class' => Proprietaire::class,
                'choice_label' => function(Proprietaire $proprietaire) {
                    return $proprietaire->getAppartementAbsoluteName(false) . ' ' . $proprietaire->getAbsoluteName();
                },
                'placeholder' => $this->translator->trans('select-choose'),
                'label' => $this->translator->trans('cotisation.proprietaire'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.leaveAt IS NULL')
                        ->orderBy('p.appartement', 'ASC');
                },
            ])
            ->add('preuves', PreuvesType::class, [
                'label' => $this->translator->trans('cotisation.preuves'),
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
