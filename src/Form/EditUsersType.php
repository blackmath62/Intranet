<?php

namespace App\Form;

use App\Entity\Main\Services;
use App\Entity\Main\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir une adresse Email',
                    ]),
                ],
                'disabled' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('pseudo', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un pseudo',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('commercial', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('interne', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('exterieur', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('fonction', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('portable', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('service', EntityType::class, [
                'class' => Services::class,
                'required' => true,
                'choice_label' => 'title',
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control col-12 mt-2 mb-2',
                    'data-placeholder' => 'Selectionnez le service',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.id <> 12')
                        ->andWhere('s.id <> 8')
                        ->orderBy('s.title', 'ASC');
                },

            ])
            ->add('roles', ChoiceType::class, [
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
                    'BUREAU_RB' => "ROLE_BUREAU_RB",
                    'BOSS' => "ROLE_BOSS",
                    'COMPTA' => "ROLE_COMPTA",
                    'ADMIN MONTEUR' => "ROLE_ADMIN_MONTEUR",
                    'MONTEUR' => "ROLE_MONTEUR",
                    'LOGISTIQUE' => "ROLE_LOGISTIQUE",
                    'CONGES' => "ROLE_CONGES",
                    'INFORMATIQUE' => "ROLE_INFORMATIQUE",
                    'RESPONSABLE SECTEUR' => "ROLE_RESPONSABLE_SECTEUR",
                    'COMMERCIAL' => "ROLE_COMMERCIAL",
                    'RSE' => "ROLE_RSE",
                ],
                'expanded' => false,
                'multiple' => true,
                'required' => true,
                'label' => 'Rôles',
                'attr' => [
                    'class' => 'form-control col-12 select2',
                    'data-placeholder' => 'Selectionnez les roles',
                ],
            ])

            ->add('Modifier', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark m-2 float-right'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
