<?php

namespace App\Form;

use App\Entity\Main\fscListMovement;
use App\Entity\Main\MovBillFsc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FactureFournisseursFscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('ventilations', EntityType::class, [
            'class' => fscListMovement::class,
            'choice_label' => 'notreRef',
            'choice_name' => 'id',
            'multiple' => true,
            'expanded' => false,
            'by_reference' => false,
            'required' => false,
            'attr' => [
                'class' => 'select2 form-control',
            ],
            'label' => false,
        ])
            ->add('ajouter', SubmitType::class,[
                    'attr' => ['class' => 'form-control btn btn-secondary'],
                    'label' => 'Modifier les achats liés'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MovBillFsc::class,
        ]);
    }
}
