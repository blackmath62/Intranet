<?php

namespace App\Form;

use App\Entity\Main\Status;
use App\Entity\Main\Societe;
use App\Entity\Main\Tickets;
use App\Entity\Main\Services;
use App\Entity\Main\Priorities;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un titre'
                    ])
                    ],
                    'required' => true,
                    'attr' => [
                        'class' => 'col-12 form-control',
                        'placeholder' => 'L\'objet de votre ticket'
                    ]
            ])
            ->add('content', TextareaType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un Contenu détaillé'
                    ])
                    ],
                    'required' => false,
                    'attr' => [
                        'class' => 'col-12 form-control textarea',
                        'placeholder' => 'Détail de votre probléme'
                    ],
                    'label' => 'Détail de la demande',
            ])
            ->add('service', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'title',
                'choice_name' => 'id',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Service concerné',
            ])
            ->add('statu', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'title',
                'choice_name' => 'id',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('societe', EntityType::class, [
                'class' => Societe::class,
                'choice_label' => 'nom',
                'choice_name' => 'id',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Société concernée',
            ])
            ->add('priority', EntityType::class, [
                'class' => Priorities::class,
                'choice_label' => 'title',
                'choice_name' => 'id',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Degrés d\'urgence',
            ])
            ->add('file', FileType::class,[
                'label' => "Le Fichier",
                'multiple' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control btn btn-default'
                ]
        ])
            ->add('poster', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark mt-3']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
