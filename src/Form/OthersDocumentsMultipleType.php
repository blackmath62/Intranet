<?php

namespace App\Form;

use App\Entity\Main\OthersDocuments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OthersDocumentsMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de selectionner au moins un fichier',
                    ]),
                ],
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => true,
                'attr' => ['class' => 'form-control col-12 text-center'],
            ])
            ->add('importer', SubmitType::class, [
                'attr' => ['class' => 'form-control col-4 text-center btn btn-dark mt-2 float-right'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OthersDocuments::class,
        ]);
    }
}
