<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintEmplType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('empl1', TextType::class, [
            'label' => 'Emplacement début',
            'label_attr' => ['class' => 'd-none'],
            'attr' => [
                'class' => 'form-control',
            ],
        ])
            ->add('empl2', TextType::class, [
                'label' => 'Emplacement fin',
                'label_attr' => ['class' => 'd-none'],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('imprimer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary col-12'],
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
