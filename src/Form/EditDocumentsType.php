<?php

namespace App\Form;

use App\Entity\Societe;
use App\Entity\Documents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditDocumentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un titre'
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control'
                ]
            ])
            ->add('beginningDate', DateType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date du document",
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'col-12 form-control js-datepicker'
                ]
            ])
            ->add('endDate', DateType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date de fin du document",
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'col-12 form-control js-datepicker'
                ]
            ])
            ->add('Modifier', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark mt-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Documents::class,
        ]);
    }
}
