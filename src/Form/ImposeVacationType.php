<?php

namespace App\Form;

use App\Entity\Main\Users;
use App\Entity\Main\Holiday;
use App\Entity\Main\HolidayTypes;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ImposeVacationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
        ->add('start', DateType::class, [
            'widget' => 'single_text',
            'required' => true,
            'label' => "Date début (inclus)",
            'attr' => ['class' => 'form-control col-12 col-sm-2 text-center'],
            'label_attr' => ['class' => 'col-12 col-sm-1 mt-3 text-center']
        ])
        ->add('sliceStart', ChoiceType::class,[
            'choices' => [
                'Journée' => "DAY",
                'Matin' => "AM",
                'Aprés-midi' => "PM",
            ],
            'expanded' => false,
            'multiple' => false,
            'label' => 'Tranche début',
            'attr' => ['class' => 'form-control col-12 col-sm-1 text-center'],
        ])
        ->add('end', DateType::class,[
            'widget' => 'single_text',
            'required' => true,
            //'data' => date_time_set(new \DateTime("now"), 23, 59),
            'label' => "Date fin (inclus)",
            'attr' => ['class' => 'form-control col-12 col-sm-2 text-center'],
            'label_attr' => ['class' => 'col-12 col-sm-1 text-center mt-3']
        ])
        ->add('sliceEnd', ChoiceType::class,[
            'choices' => [
                'Journée' => "DAY",
                'Matin' => "AM",
                'Aprés-midi' => "PM",
            ],
            'expanded' => false,
            'multiple' => false,
            'label' => 'Tranche fin',
            'attr' => ['class' => 'form-control col-6 col-sm-1 text-center'],
        ])
        ->add('holidayType',EntityType::class,[
            'class' => HolidayTypes::class,
            'choice_label' => 'name',
            'label' => 'Type',
            'attr' => ['class' => 'mr-3 form-control col-12 col-sm-2 text-center'],
            'label_attr' => ['class' => 'col-12 col-sm-1 text-center mt-3']
        ])
            ->add('user', EntityType::class, [
                // looks for choices from this entity
                'class' => Users::class,
                'choice_label' => 'email',
                'label' => 'Salarié(e)s',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                              ->where('u.closedAt IS NULL ')
                              ->orderBy('u.email', 'ASC');
                },
                'attr' => ['class' => 'mr-3 form-control col-12 text-center'],
            ])
            ->add('details', HiddenType::class)
            ->add('Envoyer', SubmitType::class,[
                'attr' => ['class' => 'col-12 col-sm-1 form-control btn btn-dark mt-3 float-right']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            
        ]);
    }
}
