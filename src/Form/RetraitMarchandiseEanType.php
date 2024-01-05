<?php

namespace App\Form;

use App\Entity\Main\RetraitMarchandisesEan;
use App\Repository\Divalto\ArtRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RetraitMarchandiseEanType extends AbstractType
{
    private $repoArt;
    protected $requestStack;
    public function __construct(ArtRepository $repoArt, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->repoArt = $repoArt;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arts = $this->repoArt->getAllEmpl(1);
        $arrayArts = [];
        foreach ($arts as $art) {
            $arrayArts[$art['empl']] = $art['empl'];
        }

        $builder
            ->add('chantier', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un chantier',
                    ]),
                ],
                'required' => true,
                'attr' => ['class' => 'form-control col-12',
                    'placeholder' => 'Nom du Chantier ( Affaire )'],
            ])
            ->add('ean', SearchType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control col-12',
                    'placeholder' => 'Je cherche ....'],
            ])
            ->add('retour', CheckboxType::class, array(
                'label' => 'Retour',
                'required' => false,
                'attr' => ['class' => 'custom-control-input custom-control-input-danger'],
                'label_attr' => ['class' => 'custom-control-label'],
                'mapped' => false,
                'data' => false,
            ))
            ->add('qte', NumberType::class, array(
                'required' => true,
                'attr' => ['class' => 'form-control col-12',
                    'placeholder' => 'Qte/Uv',
                ],
            ))
            ->add('location', ChoiceType::class, [
                'required' => true,
                'label' => 'Emplacement',
                'attr' => ['class' => 'form-control col-12'],
                'choices' => $arrayArts,
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('stockFaux', CheckboxType::class, array(
                'label' => 'Stock Faux ?',
                'required' => false,
                'attr' => ['class' => 'custom-control-input custom-control-input-warning'],
                'label_attr' => ['class' => 'custom-control-label'],
            ))
            ->add('save', SubmitType::class, [
                'label' => '<i class="fas fa-solid fa-cart-arrow-down fa-lg"></i> Ajouter au panier',
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
