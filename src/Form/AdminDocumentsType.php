<?php

namespace App\Form;

use App\Entity\Main\Documents;
use App\Entity\Main\Societe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminDocumentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un Titre',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('url', FileType::class, [
                'label' => "Le Fichier",
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '6M',
                        'mimeTypes' => [
                            "image/png",
                            "image/jpeg",
                            "image/jpg",
                            "application/pdf",
                            "application/x-pdf",
                        ],
                        'mimeTypesMessage' => 'Les formats autorisés sont PDF, JPG, PNG',
                    ]),
                ],
                'attr' => [
                    'class' => 'col-12 form-control btn btn-primary',
                ],
            ])
            ->add('beginningDate', DateType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date de Début",
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control js-datepicker',
                ],
            ])
            ->add('endDate', DateType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date de fin de validité",
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control js-datepicker',
                ],
            ])
            ->add('societe', EntityType::class, [
                // looks for choices from this entity
                'class' => Societe::class,
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Documents::class,
        ]);
    }
}
