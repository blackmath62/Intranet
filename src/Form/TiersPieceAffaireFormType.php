<?php

namespace App\Form;

use App\Repository\Divalto\CliRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TiersPieceAffaireFormType extends AbstractType
{
    private $repoCli;
    protected $requestStack;
    public function __construct(CliRepository $repoCli, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->repoCli = $repoCli;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $clis = $this->repoCli->getCodeAffaire();

        $array = [];
        foreach ($clis as $cli) {
            if (!empty($cli['affaire'])) {
                $array[$cli['affaire'] . ' (' . $cli['lib'] . ')'] = $cli['affaire'];
            }
        }

        $builder

            ->add('tiers', ChoiceType::class, [
                'choices' => [
                    'Client' => 'C',
                    'Fournisseur' => 'F',
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Tiers',
                'attr' => ['class' => 'text-center form-control mr-3'],
            ])
            ->add('affaire', ChoiceType::class, [
                'choices' => $array,
                'expanded' => false,
                'required' => false,
                'multiple' => false,
                'label' => 'Code Affaire',
                'attr' => [
                    'class' => 'select2bs4 form-control',
                    'disabled' => true,
                ],

            ])
            ->add('piece', IntegerType::class, [
                'label' => 'Numéro facture',
                'attr' => ['class' => 'text-center form-control mr-3'],
            ])
            ->add('modifier', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-danger',
                    'disabled' => true,
                ],
                'label' => 'Modifier la piéce',
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
