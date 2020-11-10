<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditUsersType extends AbstractType
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
                    'required' => true,
                    'attr' => [
                        'class' => 'col-12 form-control'
                    ]
            ])
            ->add('pseudo', TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un pseudo'
                    ])
                    ],
                    'required' => true,
                    'attr' => [
                        'class' => 'col-12 form-control'
                    ]
            ])
            ->add('img', UrlType::class,[
                    'attr' => [
                        'class' => 'col-12 form-control'
                    ]
            ]);
                $builder->add('roles', ChoiceType::class,[
                    'attr' => [
                    'class' => ''
                    ],
                    'choices' => [
                        'Utilisateur' => "ROLE_USER",
                        'Lhermitte' => "ROLE_LHERMITTE",
                        'Roby' => "ROLE_ROBY",
                        'Administrateur' => "ROLE_ADMIN",
                        'HP' => "ROLE_HP",
                        'EV' => "ROLE_EV",
                        'MA' => "ROLE_MA",
                        'ME' => "ROLE_ME",
                    ],
                    'choice_attr' => [
                        'Utilisateur' => ['class' => 'm-3 text-primary'],
                        'Lhermitte' => ['class' => 'm-3'],
                        'Roby' => ['class' => 'm-3'],
                        'Administrateur' => ['class' => 'm-3'],
                        'HP' => ['class' => 'm-3'],
                        'EV' => ['class' => 'm-3'],
                        'MA' => ['class' => 'm-3'],
                        'ME' => ['class' => 'm-3']
                    ],
                    'expanded' => true,
                    'multiple' => true,
                    'label' => 'Rôles'
                ]);
            
            $builder->add('Modifier', SubmitType::class,[
                'attr' => ['class' => 'btn btn-dark']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
