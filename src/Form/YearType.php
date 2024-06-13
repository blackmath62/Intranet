<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YearType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $anneeActuelle = date("Y");
        $N1 = date("Y") - 1;
        $N2 = date("Y") - 2;
        $N3 = date("Y") - 3;
        $N4 = date("Y") - 4;
        $builder
            ->add('year', ChoiceType::class, [
                'choices' => [
                    $anneeActuelle => $anneeActuelle,
                    $N1 => $N1,
                    $N2 => $N2,
                    $N3 => $N3,
                    $N4 => $N4,

                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Année',
                'label_attr' => ['class' => 'd-none'],
            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary'],
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
