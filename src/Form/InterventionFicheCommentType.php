<?php

namespace App\Form;

use App\Entity\Main\InterventionFicheMonteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class InterventionFicheCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre commentaire',
                    ]),
                ],
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Vous pouvez saisir un commentaire ici....',
                ],
                'label' => 'Nouveau commentaire...',
                'required' => false,
            ])
            ->add('ajouter', SubmitType::class, [
                'label' => "Ajouter",
                'attr' => ['class' => 'col-12 form-control btn btn-primary mt-3 float-right'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterventionFicheMonteur::class,
        ]);
    }
}
