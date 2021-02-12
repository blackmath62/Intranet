<?php

namespace App\Form;

use App\Entity\Main\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

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
        ->add('img', FileType::class,[
                'attr' => [
                    'class' => 'col-12 form-control'
                ],

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'data_class' => null,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2000k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Merci de selectionner un Fichier JPG ou PNG',
                    ])
                ],
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
