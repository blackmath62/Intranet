<?php

namespace App\Form;

use DateTime;
use App\Entity\Main\ConduiteDeTravauxMe;
use App\Entity\Main\FournisseursDivalto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DateDebutDateFinFournisseursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateType::class,[
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date Début : ",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center']
            ])
            ->add('end', DateType::class,[
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date fin : ",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center']
            ])
            ->add('fournisseurs', EntityType::class, [
                'class' => FournisseursDivalto::class,
                'choice_label' => 'nom',
                'choice_name' => 'tiers',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2 form-control',
                ],
                'label' => 'Selectionnez le/les fournisseur(s)',
            ])
            ->add('type', ChoiceType::class,[
                'choices' => [
                    'Tiers | Référence | Sref1 | Sref2 | Désignation | Qte | Prix Unitaire | Montant' => "basique",
                    'Tiers | Référence | Sref1 | Sref2 | Désignation | Op | Date Facture | Facture | Qte | Prix Unitaire | Montant | Adresse Livraison' => "dateOp",
                ],
                'choice_attr' => [
                    'Tiers | Référence | Sref1 | Sref2 | Désignation | Qte | Prix Unitaire | Montant' => ['class' => 'm-3'],
                    'Tiers | Référence | Sref1 | Sref2 | Désignation | Op | Date Facture | Facture | Qte | Prix Unitaire | Montant | Adresse Livraison' => ['class' => 'm-3'],
                ],
                'expanded' => false,
                'required' => true,
                'multiple' => false,
                'label' => 'Selectionnez le type d\'export',
                'attr' => ['class' => 'form-control'],
               
            ])
            ->add('filtrer', SubmitType::class,[
                'attr' => ['class' => 'btn btn-secondary mt-3 float-right']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            
        ]);
    }
}
