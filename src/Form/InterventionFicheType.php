<?php

namespace App\Form;

use App\Entity\Main\InterventionFicheMonteur;
use App\Entity\Main\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionFicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intervenant', EntityType::class, [
                'class' => Users::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.service = 9');
                },
                'choice_label' => 'Pseudo',
                'choice_name' => 'id',
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control select2bs4',
                ],
                'label' => "Fiche d'intervention pour ...",
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date",
                'attr' => ['class' => 'form-control col-12 col-sm-12 text-center'],
                'label_attr' => ['class' => 'col-12 col-sm-12 mt-3 text-center'],
            ])
            ->add('pension', ChoiceType::class, [
                'choices' => [
                    'Hotel' => "Hotel",
                    'Panier Repas Midi' => "Panier Repas Midi",
                    'Panier Repas Soir' => "Panier Repas Soir",
                    'Restaurant Repas Midi' => "Restaurant Repas Midi",
                    'Restaurant Repas Soir' => "Restaurant Repas Soir",
                    'Client Repas Midi' => "Client Repas Midi",
                    'Client Repas Soir' => "Client Repas Soir",
                ],
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'label' => 'Pension',
                'attr' => [
                    'class' => 'form-control col-12 select2',
                    'data-placeholder' => 'Selectionnez la pension',
                ],
            ])
            /*->add('commentaire', TextareaType::class, [
        'required' => false,
        'label_attr' => ['class' => 'col-12 text-center mt-3'],
        'attr' => [
        'class' => 'col-12 form-control textarea',
        'placeholder' => 'Vous pouvez saisir un commentaire',
        ],
        'label' => 'Commentaire',
        ])*/
            ->add('envoyer', SubmitType::class, [
                'attr' => ['class' => 'col-12 form-control btn btn-dark mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterventionFicheMonteur::class,
        ]);
    }
}
