<?php

namespace App\Form;

use App\Entity\Main\Prestataire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PrestataireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner ce champs',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-2 form-control',
                ],
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner ce champs',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-2 form-control',
                ],
            ])
            ->add('affiliation', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner ce champs',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-2 form-control',
                ],
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner ce champs',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-2 form-control',
                ],
            ])
            ->add('color', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir une couleur',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-2 form-control my-colorpicker2',
                ],
            ])
            ->add('img', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'col-2 form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestataire::class,
        ]);
    }
}
