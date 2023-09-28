<?php

namespace App\Form;

use App\Entity\Main\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir une adresse Email',
                    ]),
                ],
                'disabled' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('pseudo', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un pseudo',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('img', FileType::class, [
                'attr' => [
                    'class' => 'col-12 form-control',
                ],

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'mapped' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '6M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Merci de selectionner un Fichier JPG ou PNG',
                    ]),
                ],
            ])
            ->add('bornAt', DateType::class, [
                'widget' => 'single_text',
                'label' => "Date de naissance",
                'attr' => ['class' => 'form-control text-center'],
                'label_attr' => ['class' => ' text-center'],
            ])
            ->add('Modifier', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-dark m-3',
                ],
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
