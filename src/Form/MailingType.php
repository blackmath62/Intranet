<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MailingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('de', EmailType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir l\'adresse Email d\'envoie'
                    ])
                    ],
                    'disabled' => false,
                    'attr' => [
                        'class' => 'col-12 form-control'
                    ]
            ])
            ->add('a', EmailType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'a envoyer à ....'
                    ])
                    ],
                    'disabled' => true,
                    'required' => false,
                    'attr' => [
                        'class' => 'col-12 form-control'
                    ]
            ])
            ->add('dossier', ChoiceType::class,[
                'choices' => [
                    '1' => "1",
                    '3' => "3",
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Dossier',
                'attr' => ['class' => 'form-control col-6 col-sm-1 text-center'],
            ])
            ->add('tiers', ChoiceType::class,[
                'choices' => [
                    'Fournisseurs' => "FOU",
                    'Clients' => "CLI",
                ],
                'disabled' => true,
                'expanded' => false,
                'multiple' => false,
                'label' => 'Type Tiers',
                'attr' => ['class' => 'form-control col-6 col-sm-1 text-center'],
            ])
            ->add('objet', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un objet'
                    ])
                ],
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control'
                ]
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre Message'
                    ])
                ],
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Veuillez saisir votre message'
                ],
                'label' => 'Message',
            ])
            ->add('envoyer', SubmitType::class,[
                'attr' => ['class' => 'btn btn-primary m-2 col-3 float-right'],
                'label' => 'Envoyer le mailing'
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
