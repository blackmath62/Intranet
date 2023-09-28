<?php

namespace App\Form;

use App\Entity\Main\fscListMovement;
use App\Entity\Main\MovBillFsc;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureFournisseursFscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('ajouter', SubmitType::class, [
                'attr' => ['class' => 'form-control btn btn-secondary'],
                'label' => 'Modifier les achats liés',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MovBillFsc::class,
        ]);
    }
}
