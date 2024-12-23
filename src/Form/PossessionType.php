<?php

namespace App\Form;

use App\Entity\Possession;
use App\Form\Type\AppartementFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PossessionType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('appartement', AppartementFieldType::class)
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
            ])
            ->add('leaveAt', null, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('proprietaire.leaveAt'),
                'attr' => ['class' => 'leaveAt'],
                'row_attr' => ['class' => ' '],
                'help' => $this->translator->trans('proprietaire.leaveAt-help')
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $possession = $event->getData();
            $isCurrentOwner = true;
            if ($possession instanceof Possession) {
                $isCurrentOwner = !$possession->getLeaveAt();
            }
            $event->getForm()
                ->get('isCurrentOwner')
                ->setData($isCurrentOwner);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Possession::class,
        ]);
    }
}
