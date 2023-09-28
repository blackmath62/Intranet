<?php

namespace App\Form;

use App\Entity\Main\FAQ;
use App\Entity\Main\Logiciel;
use App\Entity\Main\SectionSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un titre',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre commentaire',
                    ]),
                ],
                'required' => false,
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Veuillez saisir votre contenu',
                ],
                'label' => 'Contenu',
            ])
            ->add('logiciel', EntityType::class, [
                'class' => Logiciel::class,
                'choice_label' => 'nom',
                'choice_name' => 'id',
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Logiciel',
            ])
            ->add('search', EntityType::class, [
                'class' => SectionSearch::class,
                'choice_label' => 'nom',
                'choice_name' => 'id',
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Mot clé',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FAQ::class,
        ]);
    }
}
