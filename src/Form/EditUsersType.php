<?php

namespace App\Form;

use App\Entity\Main\Services;
use App\Entity\Main\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
                    'disabled' => true,
                    'attr' => [
                        'class' => 'col-2 form-control'
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
                        'class' => 'col-2 form-control'
                    ]
            ])
            ->add('commercial', TextType::class,[
                    'required' => false,    
                    'attr' => [
                        'class' => 'col-2 form-control'
                    ]
            ])
            ->add('service', EntityType::class,[
                'class' => Services::class,
                'required' => true,
                'choice_label' => 'title',
                'multiple' => false,
                'expanded'=>false,
                'attr' => [
                    'class' => 'form-control col-2 mt-2 mb-2',
                    'data-placeholder' => 'Selectionnez le service'
                ],
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('s')
                            ->where('s.id <> 12')
                            ->andWhere('s.id <> 8')
                            ->orderBy('s.title', 'ASC');
                },

                
            ])
                ->add('roles', ChoiceType::class,[
                    'choices' => [
                        'Utilisateur' => "ROLE_USER",
                        'Lhermitte' => "ROLE_LHERMITTE",
                        'Roby' => "ROLE_ROBY",
                        'Administrateur' => "ROLE_ADMIN",
                        'HP' => "ROLE_HP",
                        'EV' => "ROLE_EV",
                        'MA' => "ROLE_MA",
                        'ME' => "ROLE_ME",
                        'RB' => "ROLE_RB",
                        'BOSS' => "ROLE_BOSS",
                        'COMPTA' => "ROLE_COMPTA",
                        'CONGES' => "ROLE_CONGES",
                        'INFORMATIQUE' => "ROLE_INFORMATIQUE",
                        'RESPONSABLE SECTEUR' => "ROLE_RESPONSABLE_SECTEUR",
                        'COMMERCIAL' => "ROLE_COMMERCIAL",
                    ],
                    'choice_attr' => [
                        'Utilisateur' => ['class' => 'm-3'],
                        'Lhermitte' => ['class' => 'm-3'],
                        'Roby' => ['class' => 'm-3'],
                        'Administrateur' => ['class' => 'm-3'],
                        'HP' => ['class' => 'm-3'],
                        'EV' => ['class' => 'm-3'],
                        'MA' => ['class' => 'm-3'],
                        'ME' => ['class' => 'm-3'],
                        'RB' => ['class' => 'm-3'],
                        'BOSS' => ['class' => 'm-3'],
                        'COMPTA' => ['class' => 'm-3'],
                        'CONGES' => ['class' => 'm-3'],
                        'INFORMATIQUE' => ['class' => 'm-3'],
                        'RESPONSABLE SECTEUR' => ['class' => 'm-3'],
                        'COMMERCIAL' => ['class' => 'm-3'],
                    ],
                    'expanded' => true,
                    'multiple' => true,
                    'label' => 'Rôles'
                ])
            
            ->add('Modifier', SubmitType::class,[
                'attr' => ['class' => 'btn btn-dark float-right']
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
