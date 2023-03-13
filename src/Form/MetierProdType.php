<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MetierProdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produits', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un objet',
                    ]),
                ],
                'required' => false,
                'label_attr' => ['class' => 'd-none'],
                'attr' => [
                    'class' => 'col-12 form-control',
                    'placeholder' => 'Commence par...',
                ],
            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => ['class' => 'form-control btn btn-success'],
                'label' => 'Filtrer',
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
