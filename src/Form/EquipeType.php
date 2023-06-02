<?php

namespace App\Form;

use App\Entity\Main\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un titre',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('textColor', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir une couleur',
                    ]),
                ],
                'required' => true,
                'label' => 'Couleur du texte',
                'attr' => [
                    'class' => 'col-12 form-control my-colorpicker2',
                ],
            ])
            ->add('backgroundColor', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir une couleur',
                    ]),
                ],
                'required' => true,
                'label' => 'Couleur du fond',
                'attr' => [
                    'class' => 'col-12 form-control my-colorpicker2',
                ],
            ])
            ->add('creer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark mt-3'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
