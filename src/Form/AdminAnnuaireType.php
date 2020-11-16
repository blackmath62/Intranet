<?php

namespace App\Form;

use App\Entity\Societe;
use App\Entity\Annuaire;
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

class AdminAnnuaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('interne',IntegerType::class)
            ->add('nom', TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un pseudo'
                    ])
                    ],
                    'required' => true
                    ])
            ->add('exterieur', TextType::class,[
                    'required' =>false
                    ])
            ->add('mail', EmailType::class,[
                'required' =>false
                ])
            ->add('fonction', TextType::class,[
                'required' =>false
                ])
            ->add('portable', TextType::class,[
                'required' =>false
                ])
            ->add('societe', EntityType::class, [
                // looks for choices from this entity
                'class' => Societe::class,
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('Modifier', SubmitType::class,[
                'attr' => ['class' => 'col-1 form-control btn btn-dark m-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annuaire::class,
        ]);
    }
}
