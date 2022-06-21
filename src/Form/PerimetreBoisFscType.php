<?php

namespace App\Form;

use App\Entity\Main\fscListMovement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PerimetreBoisFscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('perimetreBois', ChoiceType::class,[
                'choices' => [
                    'Non Renseigné' => "Non Renseigné",
                    'Fsc Mix Crédit' => "Fsc Mix Crédit",
                    'Fsc 100 %' => "Fsc 100 %",
                    'Fsc Mix' => "Fsc Mix",
                ],
                'choice_attr' => [
                    'Non Renseigné' => ['class' => 'm-3 text-dark'],
                    'Fsc Mix Crédit' => ['class' => 'm-3'],
                    'Fsc 100 %' => ['class' => 'm-3'],
                    'Fsc Mix' => ['class' => 'm-3'],
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Périmétre Bois'
            ])
        
        ->add('ModifierPerimetre', SubmitType::class,[
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
