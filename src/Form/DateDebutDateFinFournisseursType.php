<?php

namespace App\Form;

use DateTime;
use App\Entity\Main\ConduiteDeTravauxMe;
use App\Entity\Main\FournisseursDivalto;
use App\Repository\Divalto\ArtRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\Divalto\FouRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DateDebutDateFinFournisseursType extends AbstractType
{
    private $repoFou;
    protected $requestStack;
    private$repoArt;
    public function __construct(FouRepository $repoFou, ArtRepository $repoArt, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->repoFou = $repoFou;
        $this->repoArt = $repoArt;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dos = $this->requestStack->getCurrentRequest()->get('dos');
        $familles = $this->repoArt->getFamilleProduitOuvertParDossier($dos);
        $fous = $this->repoFou->getListFou($dos);
        
        $arrayFous = [];
        foreach ($fous as $fou) {
            if (!empty($fou['tiers'])) {
                $arrayFous[$fou['tiers']] = $fou['tiers'];
            }
        }
        $arrayFamilles = [];
        foreach ($familles as $famille) {
            if (!empty($famille['famille'])) {
                $arrayFamilles[$famille['famille']] = $famille['famille'];
            }
        }

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
            ->add('fournisseurs', ChoiceType::class,[
                'choices' => $arrayFous,
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'label' => 'Fournisseurs',
                'attr' => ['class' => 'select2 form-control'],
               
            ])
            ->add('familles', ChoiceType::class,[
                'choices' => $arrayFamilles,
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'label' => 'Familles produits',
                'attr' => ['class' => 'select2 form-control'],
               
            ])
            ->add('type', ChoiceType::class,[
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
