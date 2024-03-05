<?php

namespace App\Form;

use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\FouRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateDebutDateFinFournisseursType extends AbstractType
{
    private $repoFou;
    protected $requestStack;
    private $repoArt;
    public function __construct(FouRepository $repoFou, ArtRepository $repoArt, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->repoFou = $repoFou;
        $this->repoArt = $repoArt;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dos = $this->requestStack->getCurrentRequest()->get('dos');
        $familles = $this->repoArt->getFamilleProduitOuvertParDossier($dos);
        $fous = $this->repoFou->getListFou($dos);

        $arrayFous = [];
        foreach ($fous as $fou) {
            if (!empty($fou['tiers'])) {
                $arrayFous[$fou['nom']] = $fou['tiers'];
            }
        }
        $arrayFamilles = [];
        foreach ($familles as $famille) {
            if (!empty($famille['famille'])) {
                $arrayFamilles[$famille['famille']] = $famille['famille'];
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
            ->add('fournisseurs', ChoiceType::class, [
                'choices' => $arrayFous,
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'label' => 'Fournisseurs',
                'attr' => ['class' => 'select2 form-control'],

            ])
            ->add('familles', ChoiceType::class, [
                'choices' => $arrayFamilles,
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'label' => 'Familles produits',
                'attr' => ['class' => 'select2 form-control'],

            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Tiers | Famille | Référence | Sref1 | Sref2 | Désignation | Qte | Prix Unitaire | Montant' => "basique",
                    'Famille | Référence | Sref1 | Sref2 | Désignation | Qte | Prix Unitaire | Montant' => "sansFournisseurs",
                    'Tiers | Famille | Référence | Sref1 | Sref2 | Désignation | Op | Date Facture | Facture | Qte | Prix Unitaire | Montant | Adresse Livraison' => "dateOp",
                ],
                'choice_attr' => [
                    'Tiers | Famille | Référence | Sref1 | Sref2 | Désignation | Qte | Prix Unitaire | Montant' => ['class' => 'm-3'],
                    'Famille | Référence | Sref1 | Sref2 | Désignation | Qte | Prix Unitaire | Montant' => ['class' => 'm-3'],
                    'Tiers | Famille | Référence | Sref1 | Sref2 | Désignation | Op | Date Facture | Facture | Qte | Prix Unitaire | Montant | Adresse Livraison' => ['class' => 'm-3'],
                ],
                'expanded' => false,
                'required' => true,
                'multiple' => false,
                'label' => 'Selectionnez le type d\'export',
                'attr' => ['class' => 'form-control'],

            ])
            ->add('metier', ChoiceType::class, [
                'choices' => [
                    'EV' => "EV",
                    'HP' => "HP",
                    'ME' => "ME",
                ],
                'choice_attr' => [
                    'EV' => ['class' => 'm-3'],
                    'HP' => ['class' => 'm-3'],
                    'ME' => ['class' => 'm-3'],
                ],
                'expanded' => true,
                'required' => false,
                'multiple' => true,
                'label' => 'Choisissez un ou plusieurs métiers (optionnel)',
                'attr' => ['class' => ''],

            ])
            ->add('tiers', ChoiceType::class, [
                'choices' => [
                    'Client' => "C",
                    'Fournisseur' => "F",
                ],
                'choice_attr' => [
                    'Client' => ['class' => 'm-3'],
                    'Fournisseur' => ['class' => 'm-3'],
                ],
                'expanded' => true,
                'required' => true,
                'multiple' => false,
                'label' => 'Type de tiers',
                'attr' => ['class' => ''],

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
