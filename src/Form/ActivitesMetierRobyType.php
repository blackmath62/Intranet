<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivitesMetierRobyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date début (inclus)",
                'attr' => ['class' => 'form-control text-center'],
                'label_attr' => ['class' => ' text-center'],
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date fin (inclus)",
                'attr' => ['class' => 'form-control text-center'],
                'label_attr' => ['class' => 'text-center'],
            ])
            ->add('Metiers', ChoiceType::class, [
                'choices' => [
                    'RB' => "RB",
                ],
                'choice_attr' => [
                    'RB' => ['class' => 'm-2'],
                ],
                'expanded' => false,
                'multiple' => false,
                'label_attr' => ['class' => 'float-left mr-2'],
                'label' => 'Métier',
                'attr' => ['class' => 'form-control'],

            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => ['class' => 'form-control btn btn-success'],
                'label' => 'Visualiser l\'activité de cette période',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
