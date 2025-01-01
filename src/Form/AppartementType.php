<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Entity\Batiment;
use App\Entity\Syndic;
use App\Service\SyndicSessionResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppartementType extends AbstractType
{

    private Syndic $syndic;

    public function __construct(private TranslatorInterface $translator, SyndicSessionResolver $syndicSessionResolver)
    {
        $this->syndic = $syndicSessionResolver->getSelectedSyndic();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', NumberType::class, [
                'label' => $this->translator->trans('appartement.nom'),
                'html5' => true,
            ])
            ->add('batiment', EntityType::class, [
                'label' => $this->translator->trans('batiment.nom'),
                'class' => Batiment::class,
                'choice_label' => 'nom',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('b')
                        ->where('b.syndic = :syndic')
                        ->setParameter('syndic', $this->syndic)
                        ->orderBy('b.nom', 'ASC');
                },
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('save')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appartement::class,
        ]);
    }
}
