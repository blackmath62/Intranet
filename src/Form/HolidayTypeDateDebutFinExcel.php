<?php

namespace App\Form;

use App\Entity\Main\Holiday;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HolidayTypeDateDebutFinExcel extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date début (inclus)",
                'attr' => ['class' => 'form-control text-center'],
                'label_attr' => ['class' => ' text-center'],
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date fin (inclus)",
                'attr' => ['class' => 'form-control text-center'],
                'label_attr' => ['class' => 'text-center'],
            ])
            ->add('send', SubmitType::class, [
                'attr' => ['class' => 'form-control btn btn-success'],
                'label' => 'Recevoir par mail une sauvegarde des tableaux de congés de cette période',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Holiday::class,
        ]);
    }
}
