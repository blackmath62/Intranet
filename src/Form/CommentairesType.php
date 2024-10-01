<?php

namespace App\Form;

use App\Entity\Main\Commentaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Vous pouvez saisir un commentaire ici....',
                ],
                'label' => 'Nouveau commentaire...',
            ])
            ->add('ajouter', SubmitType::class, [
                'label' => "Ajouter",
                'attr' => ['class' => 'col-12 form-control btn btn-primary mt-3 float-right'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }
}
