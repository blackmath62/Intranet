<?php

namespace App\Form;

use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\FouRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAndFouCodeTarifType extends AbstractType
{
    private $repoFou;
    private $repoArt;
    protected $requestStack;
    public function __construct(ArtRepository $repoArt, FouRepository $repoFou, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->repoFou = $repoFou;
        $this->repoArt = $repoArt;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fous = $this->repoFou->getListFou(1);

        $arrayFous = [];
        foreach ($fous as $fou) {
            if (!empty($fou['tiers'])) {
                $arrayFous[$fou['nom'] . ' (' . $fou['nbeProd'] . ')'] = $fou['tiers'];
            }
        }
        $codes = $this->repoArt->getListCode();

        $arrayCodes = [];
        foreach ($codes as $code) {
            if (!empty($code['tacod'])) {
                $arrayCodes[$code['tacod']] = $code['tacod'];
            }
        }

        $familles = $this->repoArt->getFamilleProduitOuvertParDossier(1);

        $arrayFamilles = [];
        foreach ($familles as $famille) {
            if (!empty($famille['famille'])) {
                $arrayFamilles[$famille['famille']] = $famille['famille'];
            }
        }

        $builder
            ->add('search', TextType::class, [
                'label' => 'Rechercher',
                'label_attr' => ['class' => 'd-none'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('fournisseurs', ChoiceType::class, [
                'choices' => $arrayFous,
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'label_attr' => ['class' => 'd-none'],
                'label' => 'Fournisseurs',
                'attr' => ['class' => 'select2 form-control'],

            ])
            ->add('familles', ChoiceType::class, [
                'choices' => $arrayFamilles,
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'label_attr' => ['class' => 'd-none'],
                'label' => 'Familles produits',
                'attr' => ['class' => 'form-control select2'],
            ])
            ->add('codeTarif', ChoiceType::class, [
                'choices' => $arrayCodes,
                'expanded' => false,
                'required' => true,
                'multiple' => true,
                'label_attr' => ['class' => 'd-none'],
                'label' => 'Codes Tarif',
                'attr' => ['class' => 'select2 form-control'],

            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
