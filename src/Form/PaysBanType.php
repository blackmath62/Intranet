<?php

namespace App\Form;

use App\Entity\Main\PaysBanFsc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaysBanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pays', TextType::class, [
                'attr' => [
                    'class' => 'form-control m-3 col-12 text-center',
                    'placeholder' => 'Veuillez renseigner le code Pays à 2 Caractéres de Divalto',
                ],
                'label' => 'Pays',
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => ['class' => 'form-control btn btn-xl btn-dark m-3'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PaysBanFsc::class,
        ]);
    }
}
