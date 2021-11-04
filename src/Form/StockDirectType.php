<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StockDirectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('metier', ChoiceType::class,[
                'choices' => [
                    'Espaces Verts / Horticulture Pépiniére' => '\'EV\', \'HP\'',
                    'Matériel Equipement' => '\'ME\'',
                    'Roby' => '\'RB\'',
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Métier',
                'attr' => ['class' => 'text-center form-control mr-3'],
            ])
            ->add('filtrer', SubmitType::class,[
                'attr' => ['class' => 'btn btn-info'],
                'label' => 'Filtrer'
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
