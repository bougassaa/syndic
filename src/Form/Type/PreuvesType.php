<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class PreuvesType extends AbstractType
{


    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
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
        ]);
    }


    public function getParent(): string
    {
        return FileType::class;
    }
}