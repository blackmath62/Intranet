<?php

namespace App\Form;

use App\Entity\Main\TypeDocumentFsc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TypeDocumentFscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner ce champs'
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-2 form-control'
                ]
            ])
            ->add('color', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir une couleur'
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-2 form-control my-colorpicker2'
                ]
            ])
            ->add('icone', TextType::class, [
                'attr' => [
                    'class' => 'col-2 form-control'
                ]
            ])
            ->add('Creer', SubmitType::class,[
                'attr' => ['class' => 'col-12 col-sm-1 form-control btn btn-dark m-3 float-right']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypeDocumentFsc::class,
        ]);
    }
}
