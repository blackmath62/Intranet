<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class EditUsersType extends AbstractType
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
            /*->add('img', FileType::class,[
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
        ])*/
                ->add('roles', ChoiceType::class,[
                    'choices' => [
                        'Utilisateur' => "ROLE_USER",
                        'Lhermitte' => "ROLE_LHERMITTE",
                        'Roby' => "ROLE_ROBY",
                        'Administrateur' => "ROLE_ADMIN",
                        'HP' => "ROLE_HP",
                        'EV' => "ROLE_EV",
                        'MA' => "ROLE_MA",
                        'ME' => "ROLE_ME",
                    ],
                    'choice_attr' => [
                        'Utilisateur' => ['class' => 'm-3 text-primary'],
                        'Lhermitte' => ['class' => 'm-3'],
                        'Roby' => ['class' => 'm-3'],
                        'Administrateur' => ['class' => 'm-3'],
                        'HP' => ['class' => 'm-3'],
                        'EV' => ['class' => 'm-3'],
                        'MA' => ['class' => 'm-3'],
                        'ME' => ['class' => 'm-3']
                    ],
                    'expanded' => true,
                    'multiple' => true,
                    'label' => 'Rôles'
                ])
            
            ->add('Modifier', SubmitType::class,[
                'attr' => ['class' => 'btn btn-dark']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
