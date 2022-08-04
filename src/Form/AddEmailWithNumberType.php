<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddEmailWithNumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class,[
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir une adresse Email'
                ])
                ],
                'attr' => [
                    'class' => 'col-4 form-control',
                    'placeholder' => 'Veuillez renseigner l\'Email concerné......'
                ],
        ])
        ->add('SecondOption', ChoiceType::class,[
            'choices' => [
                '01' => "01",
                '02' => "02",
                '03' => "03",
                '04' => "04",
                '05' => "05",
                '06' => "06",
                '07' => "07",
                '08' => "08",
                '09' => "09",
                '10' => "10",
                '11' => "11",
                '12' => "12",
                '13' => "13",
                '14' => "14",
                '15' => "15",
                '16' => "16",
                '17' => "17",
                '18' => "18",
                '19' => "19",
                '20' => "20",
                '21' => "21",
                '22' => "22",
                '23' => "23",
                '24' => "24",
                '25' => "25",
                '26' => "26",
                '27' => "27",
                '28' => "28",

            ],
            'choice_attr' => [
                '01' => ['class' => 'm-2'],
                '02' => ['class' => 'm-2'],
                '03' => ['class' => 'm-2'],
                '04' => ['class' => 'm-2'],
                '05' => ['class' => 'm-2'],
                '06' => ['class' => 'm-2'],
                '07' => ['class' => 'm-2'],
                '08' => ['class' => 'm-2'],
                '09' => ['class' => 'm-2'],
                '10' => ['class' => 'm-2'],
                '11' => ['class' => 'm-2'],
                '12' => ['class' => 'm-2'],
                '13' => ['class' => 'm-2'],
                '14' => ['class' => 'm-2'],
                '15' => ['class' => 'm-2'],
                '16' => ['class' => 'm-2'],
                '17' => ['class' => 'm-2'],
                '18' => ['class' => 'm-2'],
                '19' => ['class' => 'm-2'],
                '20' => ['class' => 'm-2'],
                '21' => ['class' => 'm-2'],
                '22' => ['class' => 'm-2'],
                '23' => ['class' => 'm-2'],
                '24' => ['class' => 'm-2'],
                '25' => ['class' => 'm-2'],
                '26' => ['class' => 'm-2'],
                '27' => ['class' => 'm-2'],
                '28' => ['class' => 'm-2'],
            ],
            'expanded' => false,
            'multiple' => false,
            'label_attr' => ['class' => 'col-1'],
            'label' => 'Jour d\'envoi : ',
            'attr' => ['class' => 'form-control col-1 text-center'],
        ])
        ->add('Ajouter', SubmitType::class,[
            'attr' => ['class' => 'btn btn-primary col-2 float-right']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
