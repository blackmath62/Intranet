<?php

namespace App\Form;

use DateTime;
use App\Entity\Main\Holiday;
use App\Entity\Main\Calendar;
use App\Entity\Main\Services;
use App\Entity\Main\HolidayTypes;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HolidayTypeDateDebutFinExcel extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date début (inclus)",
                'attr' => ['class' => 'form-control text-center'],
                'label_attr' => ['class' => ' text-center']
            ])
            ->add('end', DateType::class,[
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date fin (inclus)",
                'attr' => ['class' => 'form-control text-center'],
                'label_attr' => ['class' => 'text-center']
            ])
            ->add('send', SubmitType::class,[
                'attr' => ['class' => 'form-control btn btn-success'],
                'label' => 'Recevoir par mail une sauvegarde des tableaux de congés de cette période'
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
