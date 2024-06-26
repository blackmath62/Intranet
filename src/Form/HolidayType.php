<?php

namespace App\Form;

use App\Entity\Main\Holiday;
use App\Entity\Main\HolidayTypes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HolidayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date début (inclus)",
                'attr' => ['class' => 'form-control col-6 col-lg-1 text-center'],
                'label_attr' => ['class' => 'col-12 col-lg-2 mt-3 text-center'],
            ])
            ->add('sliceStart', ChoiceType::class, [
                'choices' => [
                    'Journée' => "DAY",
                    'Matin' => "AM",
                    'Aprés-midi' => "PM",
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Tranche début',
                'attr' => ['class' => 'form-control col-6 col-lg-1 text-center'],
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                //'data' => date_time_set(new \DateTime("now"), 23, 59),
                'label' => "Date fin (inclus)",
                'attr' => ['class' => 'form-control col-6 col-lg-1 text-center'],
                'label_attr' => ['class' => 'col-12 col-lg-2 text-center mt-3'],
            ])
            ->add('sliceEnd', ChoiceType::class, [
                'choices' => [
                    'Journée' => "DAY",
                    'Matin' => "AM",
                    'Aprés-midi' => "PM",
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Tranche fin',
                'attr' => ['class' => 'form-control col-6 col-lg-1 text-center'],
            ])
            ->add('holidayType', EntityType::class, [
                'class' => HolidayTypes::class,
                'choice_label' => 'name',
                'label' => 'Type',
                'attr' => ['class' => 'mr-3 form-control col-12 col-lg-2 text-center'],
                'label_attr' => ['class' => 'col-12 col-lg-1 text-center mt-3'],
            ])
            ->add('details', TextareaType::class, [
                'required' => false,
                'label_attr' => ['class' => 'col-12 text-center mt-3'],
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Vous pouvez saisir les précisions sur votre demande de congés si cela est nécéssaire',
                ],
                'label' => 'Détail de la demande',
            ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => ['class' => 'col-12 col-12 col-lg-1 form-control btn btn-dark mt-3 float-right'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Holiday::class,
        ]);
    }
}
