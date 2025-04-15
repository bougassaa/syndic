<?php

namespace App\Form;

use App\Entity\Possession;
use App\Entity\Proprietaire;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OnboardingType extends ProprietaireType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->remove('possessions');
        $builder->remove('save');

        $data = null;
        if ($options['possession'] instanceof Possession) {
            $data = $options['possession']->getBeginAt();
        }

        $builder->add('beginAt', DateType::class, [
            'widget' => 'single_text',
            'label' => $this->translator->trans('proprietaire.beginAt'),
            'help' => $this->translator->trans('onboarding.beginAt-help'),
            'mapped' => false,
            'data' => $data
        ])->add('save', SubmitType::class, [
            'label' => $this->translator->trans('save'),
            'attr' => [
                'class' => 'btn btn-primary btn-lg float-end',
            ],
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('possession', null);
    }
}