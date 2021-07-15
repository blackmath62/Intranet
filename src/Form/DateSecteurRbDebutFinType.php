<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DateSecteurRbDebutFinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
        ->add('Metiers', ChoiceType::class,[
            'choices' => [
                'RB' => "RB",
            ],
            'choice_attr' => [
                'RB' => ['class' => 'm-2'],
            ],
            'expanded' => true,
            'multiple' => false,
            'label_attr' => ['class' => 'float-left mr-2'],
            'label' => 'Métier',
            'attr' => ['class' => ''],
           
        ])

        ->add('Periode')
        ->add('filtrer', SubmitType::class,[
            'attr' => ['class' => 'btn btn-secondary']
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
