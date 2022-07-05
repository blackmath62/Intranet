<?php

namespace App\Form;

use DateTime;
use App\Entity\Main\ConduiteDeTravauxMe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ConduiteTravauxAlimenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebutChantier', DateTimeType::class,[
                'widget' => 'single_text',
                'required' => false,
                'label' => "Date Début",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center mt-3']
            ])
            ->add('dateFinChantier', DateTimeType::class,[
                'widget' => 'single_text',
                'required' => false,
                //'data' => date_time_set(new \DateTime("now"), 23, 59),
                'label' => "Date fin",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center mt-3']
            ])
            ->add('etat', ChoiceType::class,[
                'choices' => [
                    'En attente' => "En attente",
                    'A finir' => "A finir",
                    'En cours' => "En cours",
                    'Termine' => "Termine",
                    'Litige' => "Litige",
                ],
                'choice_attr' => [
                    'En attente' => ['class' => 'm-2 btn btn-warning'],
                    'A finir' => ['class' => 'm-2 btn btn-orange'],
                    'En cours' => ['class' => 'm-2 btn btn-primary'],
                    'Termine' => ['class' => 'm-2 btn btn-success'],
                    'Litige' => ['class' => 'm-2 btn btn-danger']
                ],
                'expanded' => FALSE,
                'multiple' => false,
                'label_attr' => ['class' => 'float-left mr-2'],
                'label' => 'Etat',
                'attr' => ['class' => 'form-control'],
               
            ])
            ->add('dureeTravaux',TextType::class,[
                'required' => false,
                'attr' => ['class' => 'form-control col-9',
                'placeholder' => "modifier Durée travaux"]
            ])
            ->add('backgroundColor',TextType::class,[
                'required' => false,
                'attr' => ['class' => 'form-control my-colorpicker2 m-2',
                'placeholder' => "Couleur du fond"]
            ])
            ->add('textColor',TextType::class,[
                'required' => false,
                'attr' => ['class' => 'form-control my-colorpicker2 m-2',
                'placeholder' => "Couleur du texte"]
            ])
            ->add('modifier', SubmitType::class,[
                'attr' => ['class' => 'btn btn-secondary mt-3 float-right']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConduiteDeTravauxMe::class,
        ]);
    }
}
