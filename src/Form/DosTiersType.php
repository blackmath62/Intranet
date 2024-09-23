<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DosTiersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dos', ChoiceType::class, [
                'choices' => [
                    'Roby' => "3",
                    'Lhermitte' => "1",
                ],
                'choice_attr' => [
                    'Roby' => ['class' => 'm-3'],
                    'Lhermitte' => ['class' => 'm-3'],
                ],
                'expanded' => false,
                'multiple' => false,
                'label_attr' => ['class' => 'd-none'],
                'label' => 'Selectionnez la société',
                'attr' => ['class' => 'form-control'],

            ])
            ->add('typeTiers', ChoiceType::class, [
                'choices' => [
                    'Clients' => "CLI",
                    'Fournisseurs' => "FOU",
                ],
                'choice_attr' => [
                    'Clients' => ['class' => 'm-3'],
                    'Fournisseurs' => ['class' => 'm-3'],
                ],
                'expanded' => false,
                'multiple' => false,
                'label_attr' => ['class' => 'd-none'],
                'label' => 'Selectionnez la société',
                'attr' => ['class' => 'form-control'],

            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark'],
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
