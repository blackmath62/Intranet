<?php

namespace App\Form;

use App\Entity\Main\InterventionFichesMonteursHeures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionFichesMonteursHeuresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Déplacement' => "deplacement",
                    'Travaux' => "Travaux",
                    'Dépôt' => "Depot",
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'label' => 'Type',
                'attr' => [
                    'class' => 'form-control col-12 select2bs4',
                    'data-placeholder' => 'Type d\'heures',
                ],
            ])
            ->add('start', TimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Heure début",
                'attr' => ['class' => 'form-control col-12 col-sm-12 text-center'],
                'label_attr' => ['class' => 'col-12 col-sm-12 mt-3 text-center'],
            ])
            ->add('end', TimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Heure Fin",
                'attr' => ['class' => 'form-control col-12 col-sm-12 text-center'],
                'label_attr' => ['class' => 'col-12 col-sm-12 mt-3 text-center'],
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => ['class' => 'col-12 col-sm-12 form-control btn btn-dark mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterventionFichesMonteursHeures::class,
        ]);
    }
}
