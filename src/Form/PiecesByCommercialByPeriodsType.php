<?php

namespace App\Form;

use App\Repository\Divalto\VrpRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PiecesByCommercialByPeriodsType extends AbstractType
{

    private $repoCom;
    protected $requestStack;
    public function __construct(VrpRepository $repoCom, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->repoCom = $repoCom;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $coms = $this->repoCom->listCommerciauxDivalto();

        $arrayCom = [];
        foreach ($coms as $com) {
            if (!empty($com['tiers'])) {
                $arrayCom[$com['nom']] = $com['tiers'];
            }
        }

        $builder
            ->add('pieces', ChoiceType::class, [
                'choices' => [
                    'Devis' => 1,
                    'Commandes' => 2,
                    'Bls' => 3,
                    'Factures' => 4,
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Piéces',
                'attr' => ['class' => 'text-center form-control col-12'],
            ])
            ->add('commerciaux', ChoiceType::class, [
                'choices' => $arrayCom,
                'expanded' => false,
                'required' => true,
                'multiple' => true,
                //'label_attr' => ['class' => 'd-none'],
                'label' => 'Commerciaux',
                'attr' => ['class' => 'select2 form-control col-12'],
            ])
            ->add('start', DateType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date début",
                'widget' => 'single_text',
                'required' => true,
                //'label_attr' => ['class' => 'd-none'],
                'attr' => [
                    'class' => 'form-control col-12',
                ],
            ])
            ->add('end', DateType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'label' => "Date fin",
                'required' => true,
                'widget' => 'single_text',
                //'label_attr' => ['class' => 'd-none'],
                'attr' => [
                    'class' => 'form-control col-12',
                ],
            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark col-12'],
                'label' => 'Filtrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
