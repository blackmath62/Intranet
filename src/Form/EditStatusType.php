<?php

namespace App\Form;

use App\Entity\Main\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
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
            ->add('backgroundColor', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir une couleur',
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
                        'message' => 'Merci de choisir une couleur',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control my-colorpicker2',
                ],
            ])
            ->add('fa', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir un fa-****** icone',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('ClosedAt', DateType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date de Fermeture",
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'col-12 form-control js-datepicker',
                ],
            ])
            ->add('Modifier', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Status::class,
        ]);
    }
}
