<?php

namespace App\Form;

use App\Entity\Main\Affaires;
use App\Repository\Divalto\CliRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreationChantierAffaireType extends AbstractType
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
        $clis = $this->repoCli->getClient();
        /*$arrayClis = [];
        foreach ($clis as $cli) {
        if (!empty($cli['tiers'])) {
        $arrayClis[$cli['tiers']] = $cli['tiers'];
        //. ', Adresse :' . $cli['rue'] . ' ' . $cli['cp'] . ' ' . $cli['ville'];
        /*$arrayClis[$cli['tiers']][$cli['rue']] = $cli['rue'];
        $arrayClis[$cli['tiers']][$cli['cp']] = $cli['cp'];
        $arrayClis[$cli['tiers']][$cli['ville']] = $cli['ville'];
        }
        }*/

        $arrayClis = [];
        foreach ($clis as $cli) {
            if (!empty($cli['tiers'])) {
                $arrayClis[$cli['nom'] . ', Adresse :' . $cli['rue'] . ' ' . $cli['cp'] . ' ' . $cli['ville']] = $cli['tiers'];
            }
        }

        $builder

            ->add('code', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control col-12 col-sm-12',
                    'placeholder' => "Code de votre chantier (arbitraire et unique)"],
            ])
            ->add('libelle', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control col-12 col-sm-12',
                    'placeholder' => "Libellé de votre chantier (arbitraire)"],
            ])
            ->add('tiers', ChoiceType::class, [
                'choices' => $arrayClis,
                'expanded' => false,
                'required' => true,
                'multiple' => false,
                'label' => 'Client',
                'attr' => ['class' => 'select2bs4 form-control'],

            ])
            ->add('duration', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control col-12 col-sm-12',
                    'placeholder' => "indiquer la Durée des travaux"],
            ])
            ->add('creer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mt-3 float-right col-12 col-sm-3'],
                'label' => 'Créer ce chantier',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affaires::class,
        ]);
    }
}
