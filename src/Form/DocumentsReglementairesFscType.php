<?php

namespace App\Form;

use App\Entity\Main\DocumentsReglementairesFsc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentsReglementairesFscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $anneeActuelle = date("Y");
        $N1 = date("Y") - 1;
        $N2 = date("Y") - 2;
        $N3 = date("Y") - 3;
        $N4 = date("Y") - 4;

        $builder
            ->add('years', ChoiceType::class, [
                'choices' => [
                    $anneeActuelle => $anneeActuelle,
                    $N1 => $N1,
                    $N2 => $N2,
                    $N3 => $N3,
                    $N4 => $N4,

                ],
                'attr' => [
                    'class' => 'form-control m-3 col-12 text-center',
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Année',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Formation Salariés' => "Formation",
                    'Sécurité' => "Securite",
                    'Administratif' => "Administratif",
                    'Exigence droit du Travail' => "Travail",
                ],
                'choice_attr' => [
                    'Formation Salariés' => ['class' => 'm-3 btn btn-xl btn-primary'],
                    'Sécurité' => ['class' => 'm-3 btn btn-xl btn-danger'],
                    'Administratif' => ['class' => 'm-3 btn btn-xl btn-success'],
                    'Exigence droit du Travail' => ['class' => 'm-3 btn btn-xl btn-warning text-dark'],
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Type de documents',
                'attr' => ['class' => 'form-control m-3 col-12 text-center'],
            ])
            ->add('files', FileType::class, [
                'label' => ' Le fichier que vous voulez déposer',
                'mapped' => false, // Tell that there is no Entity to link
                'required' => true,
                'multiple' => false,
                'attr' => ['class' => 'form-control m-3 col-12 text-center'],
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => ['class' => 'form-control btn btn-xl btn-dark m-3'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DocumentsReglementairesFsc::class,
        ]);
    }
}
