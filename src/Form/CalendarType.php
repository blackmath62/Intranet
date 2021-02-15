<?php

namespace App\Form;

use App\Entity\Main\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Veuillez saisir un titre...'
                ]
            ])
            ->add('start', DateTimeType::class, [
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second'],
                'date_widget' => 'single_text',
                'label' => "Date début",
                'attr' => ['class' => 'col-12 form-control'],
            ])
            ->add('end', DateType::class,[
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date de naissance (l'année a peu d'importance)",
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'col-12 form-control'
                ]
        ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre commentaire'
                    ])
                ],
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Veuillez saisir votre commentaire'
                ],
                'label' => 'Détail de la demande',
            ])
            ->add('all_day')
            ->add('background_color', ColorType::class,[
            'attr' => ['class' => 'form-control col-2'],
            ])
            ->add('border_color', ColorType::class,[
            'attr' => ['class' => 'form-control col-2'],
            ])
            ->add('text_color', ColorType::class,[
            'attr' => ['class' => 'form-control col-2'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}