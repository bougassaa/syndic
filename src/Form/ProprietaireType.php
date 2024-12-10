<?php

namespace App\Form;

use App\Entity\Proprietaire;
use App\Form\Type\AppartementFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProprietaireType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Proprietaire $data */
        $data = $options['data'];

        $builder
            ->add('nom', null, [
                'label' => $this->translator->trans('proprietaire.nom'),
                'attr' => ['class' => 'uppercase'],
            ])
            ->add('prenom', null, [
                'label' => $this->translator->trans('proprietaire.prenom'),
            ])
            ->add('beginAt', null, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('proprietaire.beginAt'),
                'help' => $this->translator->trans('proprietaire.beginAt-help')
            ])
            ->add('isCurrentOwner', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
                'label' => $this->translator->trans('proprietaire.isCurrentOwner'),
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'attr' => ['class' => 'isCurrentOwner'],
                'data' => !$data->getLeaveAt()
            ])
            ->add('leaveAt', null, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('proprietaire.leaveAt'),
                'attr' => ['class' => 'leaveAt'],
                'help' => $this->translator->trans('proprietaire.leaveAt-help')
            ])
            ->add('appartement', AppartementFieldType::class)
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('save')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proprietaire::class,
        ]);
    }
}