<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StatesDateFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('startDate', DateType::class,[
            'placeholder' => [
                'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
            ],
            'label' => "Date de début",
            'widget' => 'single_text',
            'label_attr' => ['class' => 'd-none'],
            'attr' => [
                'class' => 'form-control'
            ]
            ])
            ->add('endDate', DateType::class,[
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date de fin",
                'widget' => 'single_text',
                'label_attr' => ['class' => 'd-none'],
                'attr' => [
                    'class' => 'form-control'
                ]
        ])
        ->add('filtrer', SubmitType::class,[
            'attr' => ['class' => 'btn btn-dark'],
            'label' => 'Filtrer'
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
