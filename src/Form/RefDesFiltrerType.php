<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RefDesFiltrerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref', TextType::class, [
                'required' => false,
                'label_attr' => ['class' => 'd-none'],
                'attr' => [
                    'class' => 'col-12 form-control',
                    'placeholder' => 'la référence commence par...',
                ],
            ])
            ->add('des', TextType::class, [
                'required' => false,
                'label_attr' => ['class' => 'd-none'],
                'attr' => [
                    'class' => 'col-12 form-control',
                    'placeholder' => 'Contient dans la désignation...',
                ],
            ])
            ->add('cmd', CheckboxType::class, [
                'label' => 'N\'afficher que le stock',
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('direct', CheckboxType::class, [
                'label' => 'Afficher les stocks et mouvements directs',
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => ['class' => 'form-control btn btn-secondary'],
                'label' => 'Filtrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
