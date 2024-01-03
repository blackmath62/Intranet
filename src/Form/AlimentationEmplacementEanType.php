<?php

namespace App\Form;

use App\Entity\Main\AlimentationEmplacement;
use App\Repository\Divalto\ArtRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AlimentationEmplacementEanType extends AbstractType
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
        $i = 0;
        foreach ($arts as $art) {
            if ($i == 0) {
                $arrayArts['Ajouter'] = "Add";
            } elseif (!empty($art['empl'])) {
                $arrayArts[$art['empl']] = $art['empl'];
            }
            $i++;
        }

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
                'label' => 'EAN/Désignation/code (min 5 car)',
                'attr' => ['class' => 'form-control col-12'],
            ])
            ->add('oldLocation', ChoiceType::class, [
                'required' => true,
                'label' => 'Transférer ou Ajouter',
                'attr' => ['class' => 'form-control col-12'],
                'choices' => $arrayArts,
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('qte', NumberType::class, [
                'required' => true,
                'label' => 'Quantité',
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
