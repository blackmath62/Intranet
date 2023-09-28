<?php

namespace App\Form;

use App\Entity\Main\Affaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => "Date Début",
                'attr' => ['class' => 'form-control col-10 text-center'],
                'label_attr' => ['class' => 'col-12 text-center mt-3'],
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                //'data' => date_time_set(new \DateTime("now"), 23, 59),
                'label' => "Date fin",
                'attr' => ['class' => 'form-control col-10 text-center'],
                'label_attr' => ['class' => 'col-12 text-center mt-3'],
            ])
            ->add('progress', IntegerType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control col-6'],
            ])
            ->add('duration', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control col-6',
                    'placeholder' => "modifier Durée travaux"],
            ])
            ->add('modifier', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary mt-3 float-right'],
                'label' => 'Mettre à jour l\'affaire',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affaires::class,
        ]);
    }
}
