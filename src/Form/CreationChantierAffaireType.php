<?php

namespace App\Form;

use App\Entity\Main\Users;
use App\Repository\Divalto\CliRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

        $arrayClis = [];
        foreach ($clis as $cli) {
            if (!empty($cli['tiers'])) {
                $arrayClis[$cli['nom'] . ', Adresse :' . $cli['rue'] . ' ' . $cli['cp'] . ' ' . $cli['ville']] = $cli['tiers'] . '-' . $cli['nom'] . '-' . $cli['rue'] . ' ' . $cli['cp'] . ' ' . $cli['ville'];
            }
        }

        $builder

            ->add('code', TextType::class, [
                'required' => true,
                'label' => 'Code unique ( Obligatoire ! )',
                'attr' => ['class' => 'form-control col-12 col-sm-12',
                    'placeholder' => "Code de votre chantier (arbitraire et unique)"],
            ])
            ->add('libelle', TextType::class, [
                'required' => true,
                'label' => 'Libellé ( Obligatoire ! )',
                'attr' => ['class' => 'form-control col-12 col-sm-12',
                    'placeholder' => "Libellé de votre chantier (arbitraire)"],
            ])
            ->add('tiers', ChoiceType::class, [
                'choices' => $arrayClis,
                'expanded' => false,
                'required' => true,
                'multiple' => false,
                'label' => 'Client ( Obligatoire ! )',
                'attr' => ['class' => 'select2bs4 form-control'],

            ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date Début : ( Obligatoire ! )",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center'],
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date fin : ( Obligatoire ! )",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center'],
            ])
            ->add('adresse', TextType::class, [
                'required' => false,
                'label' => 'Adresse différente ? Uniquement si introuvable ( Factultatif )',
                'attr' => ['class' => 'form-control form-control',
                    'placeholder' => "elle écrase complétement l'autre adresse"],
            ])
            ->add('Equipes', EntityType::class, [
                'class' => Users::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.service = 9');
                },
                'choice_label' => 'Pseudo',
                'choice_name' => 'id',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control select2',
                ],
                'label' => "Equipe ( Obligatoire ! )",
            ])
            ->add('comment', TextareaType::class, [
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Vous pouvez saisir un commentaire ici....',
                ],
                'label' => 'Ajouter un commentaire... ( Factultatif )',
                'required' => false,
            ])
            ->add('creer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-danger mt-3 float-right col-12 col-sm-3'],
                'label' => 'Créer ce chantier',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
