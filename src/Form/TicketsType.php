<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Societe;
use App\Entity\Tickets;
use App\Entity\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
                        'class' => 'col-12 form-control'
                    ]
            ])
            ->add('content', TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un Contenu détaillé'
                    ])
                    ],
                    'required' => true,
                    'attr' => [
                        'class' => 'col-12 form-control'
                    ]
            ])
            ->add('service', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'titre',
                'choice_name' => 'id'
            ])
            ->add('statu', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'titre',
                'choice_name' => 'id'
            ])
            ->add('societe', EntityType::class, [
                'class' => Societe::class,
                'choice_label' => 'nom',
                'choice_name' => 'id'
            ])
            ->add('priority')
            ->add('Créer', SubmitType::class, [
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
