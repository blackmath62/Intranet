<?php

namespace App\Form;

use App\Entity\Main\HolidayTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditHolidayTypesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un nom',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('color', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir une couleur',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-3 form-control my-colorpicker2',
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
            'data_class' => HolidayTypes::class,
        ]);
    }
}
