<?php

namespace App\Form;

use App\Entity\Main\Status;
use App\Entity\Main\Tickets;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditStatusTicketFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("statu", EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Choisir le statut',
                'choice_name' => 'id',
            ])
            ->add('deplacer', SubmitType::class, [
                'label' => "Déplacer le ticket",
                'attr' => ['class' => 'form-control btn btn-success mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
