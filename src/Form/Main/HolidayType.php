<?php

namespace App\Form\Main;

use App\Entity\Main\Holiday;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HolidayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start')
            ->add('end')
            ->add('createdAt')
            ->add('details')
            ->add('treatmentedAt')
            ->add('user')
            ->add('treatmentedBy')
            ->add('holidayType')
            ->add('holidayStatus')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Holiday::class,
        ]);
    }
}
