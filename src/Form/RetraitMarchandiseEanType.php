<?php

namespace App\Form;

use App\Entity\Main\RetraitMarchandisesEan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RetraitMarchandiseEanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('chantier', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un chantier',
                    ]),
                ],
                'required' => true,
                'label' => 'Nom du Chantier',
                'attr' => ['class' => 'form-control col-12'],
            ])
            ->add('ean', IntegerType::class, [
                'required' => true,
                'label' => 'Code EAN',
                'attr' => ['class' => 'form-control col-12'],
            ])
            ->add('qte', NumberType::class, array(
                'label' => 'Quantité',
                'required' => true,
                'attr' => ['class' => 'form-control col-12'],
            ))
            ->add('stockFaux', CheckboxType::class, array(
                'label' => 'Stock Faux ?',
                'required' => false,
                'attr' => ['class' => ''],
                'label_attr' => ['class' => 'form-check-label'],
            ))
            ->add('save', SubmitType::class, [
                'label' => "Ajouter ce produit",
                'attr' => ['class' => 'form-control btn btn-warning mt-3 col-12'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RetraitMarchandisesEan::class,
        ]);
    }
}
