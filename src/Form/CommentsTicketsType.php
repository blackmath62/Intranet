<?php

namespace App\Form;

use App\Entity\Main\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentsTicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre'
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                    'placeholder' => 'L\'objet de votre Commentaire'
                ]
            ])
            ->add('content', TextareaType::class, [
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
            
            ->add('files', FileType::class, [
                'label' => "Le Fichier : ",
                'required' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control btn btn-default col-5 mt-3'
                ]
            ])
            ->add('Ajouter', SubmitType::class, [
                'label' => "Ajouter le commentaire",
                'attr' => ['class' => 'col-3 form-control btn btn-dark mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
