<?php

namespace App\Form;

use App\Entity\Main\AffairePiece;
use App\Entity\Main\InterventionMonteurs;
use App\Entity\Main\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionsMonteursType extends AbstractType
{
    protected $requestStack;
    protected $code;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->code = $this->requestStack->getCurrentRequest()->get('affaire');

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date Début : ",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center'],
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date fin : ",
                'attr' => ['class' => 'form-control col-12 text-center'],
                'label_attr' => ['class' => 'col-12 text-center'],
            ])
            ->add('adresse', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control form-control',
                    'placeholder' => "Adresse si différente ? "],
            ])
            ->add('backgroundColor', TextType::class, [
                'required' => false,
                'label' => "Fond : ",
                'attr' => ['class' => 'form-control form-control-color m-2',
                    'placeholder' => "Couleur du fond"],
            ])
            ->add('textColor', TextType::class, [
                'required' => false,
                'label' => "Texte : ",
                'attr' => ['class' => 'form-control form-control-color m-2',
                    'placeholder' => "Couleur du fond"],
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
                'label' => "Equipe",
            ])
            ->add('pieces', EntityType::class, [
                'class' => AffairePiece::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.affaire = ' . "'" . $this->code . "'");
                },
                'choice_label' => 'piece',
                'choice_name' => 'id',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => false,
                'attr' => [
                    'id' => 'pieces',
                    'class' => 'form-control select2',
                ],
                'label' => "Piéces",
            ])
            ->add('comment', TextareaType::class, [
                'attr' => [
                    'class' => 'col-12 form-control textarea',
                    'placeholder' => 'Vous pouvez saisir un commentaire ici....',
                ],
                'label' => 'Ajouter un commentaire...',
                'required' => false,
                'mapped' => false,
            ])
            ->add('files', FileType::class, [

                'label' => 'Ajout de fichiers',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control col-12 text-center'],
            ])
            ->add('ajouter', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success mt-3 float-right col-12 col-sm-3'],
                'label' => 'Ajouter une intervention',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterventionMonteurs::class,
        ]);
    }
}
