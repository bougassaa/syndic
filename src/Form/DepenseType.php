<?php

namespace App\Form;

use App\Entity\Depense;
use App\Entity\TypeDepense;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class DepenseType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
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
            ->add('preuves', FileType::class, [
                'label' => $this->translator->trans('depense.preuves'),
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'accept' =>  'image/jpeg, image/png, image/webp, application/pdf'
                ],
                'help' => $this->translator->trans('upload.help'),
                'constraints' => [
                    new All([
                        new File(
                            mimeTypes: [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'application/pdf',
                            ],
                            mimeTypesMessage: $this->translator->trans('upload.mimeTypesMessage'),
                        ),
                    ])
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
            'data_class' => Depense::class,
        ]);
    }
}
