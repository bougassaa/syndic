<?php

namespace App\Form;

use App\Entity\Cotisation;
use App\Entity\Syndic;
use App\Entity\Tarif;
use App\Form\Type\AppartementFieldType;
use App\Form\Type\PreuvesType;
use App\Form\Type\ProprietaireFieldType;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CotisationType extends AbstractType
{

    private Syndic $syndic;

    public function __construct(private TranslatorInterface $translator, SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $syndicSessionResolver->getSelectedSyndic();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tarif', EntityType::class, [
                'class' => Tarif::class,
                'choice_label' => fn(Tarif $tarif) => $tarif->getPeriodeYear(),
                'label' => $this->translator->trans('cotisation.tarif'),
                'attr' => ['class' => 'cotisationTarif'],
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->where('t.syndic = :syndic')
                        ->setParameter('syndic', $this->syndic)
                        ->orderBy('t.debutPeriode', 'DESC');
                }
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
            ->add('appartement', AppartementFieldType::class, [
                'attr' => ['class' => 'cotisationAppartement'],
            ])
            ->add('proprietaire', ProprietaireFieldType::class)
            ->add('preuves', PreuvesType::class, [
                'label' => $this->translator->trans('cotisation.preuves'),
            ]);

        if (!empty($options['existing_preuves'])) {
            $builder->add('existingPreuves', HiddenType::class, [
                'mapped' => false,
                'data' => json_encode($options['existing_preuves']),
                'attr' => ['class' => 'existingPreuves'],
            ]);
        }

        $builder->add('save', SubmitType::class, [
            'label' => $this->translator->trans('save')
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cotisation::class,
            'existing_preuves' => []
        ]);
    }
}
