<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('content', TextareaType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir un commentaire avec les informations demandées'
                ])
            ],
            'required' => false,
            'attr' => [
                'class' => 'col-12 form-control textarea',
                'placeholder' => 'Saisir un commentaire ici....'
            ],
            'label' => 'votre commentaire...',
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
