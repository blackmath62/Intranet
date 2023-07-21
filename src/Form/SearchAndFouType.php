<?php

namespace App\Form;

use App\Repository\Divalto\FouRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAndFouType extends AbstractType
{
    private $repoFou;
    protected $requestStack;
    public function __construct(FouRepository $repoFou, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->repoFou = $repoFou;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fous = $this->repoFou->getListFou(1);

        $arrayFous = [];
        foreach ($fous as $fou) {
            if (!empty($fou['tiers'])) {
                $arrayFous[$fou['tiers']] = $fou['tiers'];
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
                'label' => 'Fournisseurs',
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
