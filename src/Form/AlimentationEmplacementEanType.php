<?php

namespace App\Form;

use App\Entity\Main\AlimentationEmplacement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AlimentationEmplacementEanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emplacement', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un emplacement',
                    ]),
                ],
                'required' => true,
                'label' => 'Emplacement',
                'attr' => ['class' => 'form-control col-12'],
            ])
            ->add('ean', TextType::class, [
                'required' => true,
                'label' => 'Code EAN',
                'attr' => ['class' => 'form-control col-12'],
            ])
            ->add('save', SubmitType::class, [
                'label' => "Ajouter ce produit",
                'attr' => ['class' => 'form-control btn btn-warning mt-3 col-12'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AlimentationEmplacement::class,
        ]);
    }
}
