<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ProfileUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class,[
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir une adresse Email'
                ])
                ],
                'disabled' => true,
                'attr' => [
                    'class' => 'col-12 form-control'
                ]
        ])
        ->add('pseudo', TextType::class,[
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir un pseudo'
                ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control'
                ]
        ])
        ->add('img', UrlType::class,[
                'attr' => [
                    'class' => 'col-12 form-control'
                ]
        ])
        ->add('bornAt', DateType::class,[
            'placeholder' => [
                'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
            ],
            'label' => "Date de naissance",
            'widget' => 'single_text',
            'attr' => [
                'class' => 'col-12 form-control js-datepicker'
            ]
    ])
        ->add('Modifier', SubmitType::class,[
            'attr' => [
                'class' => 'btn btn-dark m-3'
                ]
        ])
    ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
