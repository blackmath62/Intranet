<?php

namespace App\Form;

use App\Repository\Divalto\ArtRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateDebutFinFamilleDossierType extends AbstractType
{
    private $repoArt;
    public function __construct(ArtRepository $repoArt)
    {
        $this->repoArt = $repoArt;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $listCategories = $this->repoArt->getFamilleProduitOuvert();
        $array = [];
        foreach ($listCategories as $category) {
            if (!empty($category['famille'])) {
                $array[$category['famille']] = $category['famille'];
            }
        }

        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date Début : ",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center'],
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date fin : ",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center'],
            ])
            ->add('dossier', ChoiceType::class, [
                'choices' => [
                    '1' => "1",
                    '3' => "3",
                ],
                'choice_attr' => [
                    '1' => ['class' => 'm-3'],
                    '3' => ['class' => 'm-3'],
                ],
                'expanded' => false,
                'required' => true,
                'multiple' => false,
                'label' => 'Dossier',
                'attr' => ['class' => 'form-control text-center'],

            ])
            ->add('fermeOuvert', ChoiceType::class, [
                'choices' => [
                    'Uniquement les articles ouverts' => "ouvert",
                    'Même les articles fermés' => "ferme",
                ],
                'choice_attr' => [
                    'ouvert' => ['class' => 'm-3'],
                    'ferme' => ['class' => 'm-3'],
                ],
                'expanded' => false,
                'required' => true,
                'multiple' => false,
                'label' => 'Articles Ouverts ? Fermés ?',
                'attr' => ['class' => 'form-control'],

            ])
            ->add('stockOuBl', ChoiceType::class, [
                'choices' => [
                    'Partir du Stock actuel' => "stock",
                    'Partir des Bls' => "bl",
                ],
                'choice_attr' => [
                    'stock' => ['class' => 'm-3'],
                    'bl' => ['class' => 'm-3'],
                ],
                'expanded' => false,
                'required' => true,
                'multiple' => false,
                'label' => 'Résultat sur Stock ou des Bls ?',
                'attr' => ['class' => 'form-control'],

            ])
            ->add('famille', ChoiceType::class, [
                'choices' => $array,
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'label' => 'Familles de produit',
                'attr' => ['class' => 'select2 form-control'],

            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary mt-3 float-right'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
