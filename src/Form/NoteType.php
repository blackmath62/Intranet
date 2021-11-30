<?php

namespace App\Form;

use App\Entity\Main\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('content', TextareaType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir une note'
                ])
            ],
            'required' => false,
            'attr' => [
                'class' => 'col-12 form-control textarea',
                'placeholder' => 'Saisissez votre note'
            ],
            'label' => 'Votre Note',
        ])
            ->add('publier', SubmitType::class, [
                'label' => "Publier",
                'attr' => ['class' => 'col-1 btn btn-dark mt-3 float-right']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
