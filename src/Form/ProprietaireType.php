<?php

namespace App\Form;

use App\Entity\Proprietaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProprietaireType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => $this->translator->trans('proprietaire.nom'),
                'attr' => ['class' => 'text-uppercase'],
            ])
            ->add('prenom', null, [
                'label' => $this->translator->trans('proprietaire.prenom'),
            ])
            ->add('possessions', CollectionType::class, [
                'label' => $this->translator->trans('proprietaire.appartements'),
                'entry_type' => PossessionType::class,
                'by_reference' => false,
                'allow_add' => true,
                'entry_options' => [
                    'label' => false,
                ]
            ])
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

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        foreach ($view->children as $child) {
            if (isset($options['row_attr'])) {
                $child->vars['row_attr']['class'] = ' ';
            }
        }
    }
}