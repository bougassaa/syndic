<?php

namespace App\Form;

use App\Entity\Banque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class BanqueType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroBanque', null, [
                'label' => $this->translator->trans('bank.account'),
            ])
            ->add('rib', null, [
                'label' => $this->translator->trans('bank.rib'),
            ])
            ->add('labelCompte', null, [
                'label' => $this->translator->trans('bank.label'),
            ])
            ->add('agence', null, [
                'label' => $this->translator->trans('bank.agence'),
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('save')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Banque::class,
        ]);
    }
}
