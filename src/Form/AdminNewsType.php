<?php

namespace App\Form;

use App\Entity\Main\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminNewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class,[
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir un titre pour la publication'
                ])
                ],
                'required' => true,
                'label' => 'Titre',
                ]) 
            ->add('content', TextareaType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un texte pour la publication'
                    ])
                    ],
                    'required' => true,
                    'attr' => [
                        'class' => 'col-12 form-control textarea',
                        'placeholder' => 'La publication'
                    ],
                    'label' => 'Contenu de la publication',
                    ])        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
