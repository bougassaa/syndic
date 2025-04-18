<?php

namespace App\Form;

use App\Entity\Depense;
use App\Entity\Syndic;
use App\Entity\TypeDepense;
use App\Form\Type\PreuvesType;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DepenseType extends AbstractType
{
    private Syndic $syndic;

    public function __construct(private TranslatorInterface $translator, SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $syndicSessionResolver->getSelectedSyndic();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'label' => $this->translator->trans('depense.type'),
                'placeholder' => $this->translator->trans('select-choose'),
                'attr' => ['class' => 'selectTypeDepense'],
                'class' => TypeDepense::class,
                'choice_label' => 'label',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->where('t.syndic = :syndic')
                        ->setParameter('syndic', $this->syndic);
                },
            ])
            ->add('paidAt', null, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('depense.paidAt'),
            ])
            ->add('montant', MoneyType::class, [
                'label' => $this->translator->trans('depense.montant'),
                'currency' => 'MAD',
                'html5' => true,
                'attr' => [
                    'type' => 'number',
                    'step' => '0.01',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => $this->translator->trans('depense.description'),
                'attr' => [
                    'rows' => 2,
                    'placeholder' => $this->translator->trans('depense.description-placeholder'),
                ],
            ])
            ->add('preuves', PreuvesType::class, [
                'label' => $this->translator->trans('depense.preuves')
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
            'data_class' => Depense::class,
            'existing_preuves' => []
        ]);
    }
}
