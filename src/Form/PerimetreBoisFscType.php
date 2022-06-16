<?php

namespace App\Form;

use App\Entity\Main\fscListMovement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PerimetreBoisFscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('perimetreBois', ChoiceType::class,[
                'choices' => [
                    'Fsc Mix Crédit' => "Fsc Mix Crédit",
                    'Fsc 100 %' => "Fsc 100 %",
                    'Fsc Mix' => "Fsc Mix",
                ],
                'choice_attr' => [
                    'Fsc Mix Crédit' => ['class' => 'm-3'],
                    'Fsc 100 %' => ['class' => 'm-3'],
                    'Fsc Mix' => ['class' => 'm-3'],
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Périmétre Bois'
            ])
        
        ->add('Modifier', SubmitType::class,[
            'attr' => ['class' => 'btn btn-dark float-right']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => fscListMovement::class,
        ]);
    }
}
