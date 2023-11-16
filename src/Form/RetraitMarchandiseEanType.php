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
                'attr' => ['class' => 'form-control col-12',
                    'placeholder' => 'Nom du Chantier'],
            ])
            ->add('ean', IntegerType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control col-12',
                    'placeholder' => 'Scannez le code EAN'],
            ])
            ->add('qte', NumberType::class, array(
                'required' => true,
                'attr' => ['class' => 'form-control col-12',
                    'placeholder' => 'Qte/Uv',
                ],
            ))
            ->add('stockFaux', CheckboxType::class, array(
                'label' => 'Stock Faux ?',
                'required' => false,
                'attr' => ['class' => 'custom-control-input custom-control-input-warning'],
                'label_attr' => ['class' => 'custom-control-label'],
            ))
            ->add('save', SubmitType::class, [
                'label' => '<i class="fas fa-cart-plus fa-lg"></i> Ajouter au panier',
                'label_html' => true,
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
