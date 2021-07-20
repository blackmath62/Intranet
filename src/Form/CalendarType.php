<?php

namespace App\Form;

use DateTime;
use App\Entity\Main\Holiday;
use App\Entity\Main\Calendar;
use App\Entity\Main\HolidayTypes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('start', DateTimeType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                    'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde'],
                'date_widget' => 'single_text',
                'time_label' => 'Heure de début',
                'time_widget' => 'single_text',
                'required' => true,
                'data' => new \DateTime("now"),
                'label' => "Date début",
                'attr' => ['class' => 'col-3 ml-3'],
            ])
            ->add('end', DateTimeType::class,[
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                    'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde'],
                'date_widget' => 'single_text',
                'time_label' => 'Heure de début',
                'time_widget' => 'single_text',
                'required' => true,
                'data' => new \DateTime("now"),
                'label' => "Date fin",
                'attr' => ['class' => 'col-3 ml-3'],
        ])
            ->add('holidayType',EntityType::class,[
                'class' => HolidayTypes::class,
                'choice_label' => 'name',
                'label' => 'Type de demande',
                'attr' => ['class' => 'ml-3 ']
            ])
            ->add('details', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Vous pouvez saisir les précisions sur votre demande de congés si cela est nécéssaire'
                ],
                'label' => 'Détail de la demande',
            ])
            ->add('Envoyer', SubmitType::class,[
                'attr' => ['class' => 'col-1 form-control btn btn-dark m-3 float-right']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Holiday::class,
        ]);
    }
}