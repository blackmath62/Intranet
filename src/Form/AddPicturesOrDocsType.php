<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPicturesOrDocsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('files', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control col-12 text-center m-2 p-2'],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Photos' => "photo_",
                    'Fiche technique' => "ft_",
                ],
                'expanded' => false,
                'multiple' => false,
                'label_attr' => ['class' => 'float-left mr-2'],
                'label' => 'Type de Documents',
                'attr' => ['class' => 'form-control m-2 p-2'],

            ])
            ->add('reference', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'id' => 'add_pictures_or_docs_reference',
                ],
            ])
            ->add('importer', SubmitType::class, [
                'attr' => ['class' => 'form-control m-2 p-2 col-12 text-center btn btn-primary'],
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
