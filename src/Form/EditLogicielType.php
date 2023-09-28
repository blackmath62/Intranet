<?php

namespace App\Form;

use App\Entity\Main\Logiciel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditLogicielType extends AbstractType
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
            ->add('backgroungColor', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un titre',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control my-colorpicker2',
                ],
            ])
            ->add('textColor', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un titre',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control my-colorpicker2',
                ],
            ])
            ->add('icon', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un icone fa-******',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('closedAt', DateType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date de fermeture",
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control js-datepicker',
                ],
            ])
            ->add('Modifier', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Logiciel::class,
        ]);
    }
}
