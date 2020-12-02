<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Societe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserRegistrationFormType extends AbstractType
{
    public function index(EntityManagerInterface $entityManager)
    {
        // These methods also return the default entity manager, but it's preferred
        // to get it by injecting EntityManagerInterface in the action method

        $entityManager = $this->getDoctrine()->getManager('default');


    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Ressaisir le mot de passe'],
                'constraints' => [
                    new NotBlank,
                    new Length(['min' => 6, 'max'=> 4096, 'minMessage' => 'le mot de passe doit faire au minimum {{ limit }} caractéres','maxMessage' => 'le mot de passe ne doit pas dépasser {{ limit }} caractéres'])
                ]
            ])
            ->add('pseudo')
            ->add("societe", EntityType::class, [
                'class' => Societe::class,
                'choice_label' => 'nom',
                'choice_name' => 'id'
            ])
            ->add('img', FileType::class, [
                'required' => false,
                'empty_data' => 'img/no-image-icon.png'
            ])
            ->add('bornAt', DateType::class,[
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date de naissance",
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'col-12 form-control'
                ]
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
