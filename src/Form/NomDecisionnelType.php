<?php

namespace App\Form;

use App\Entity\Divalto\Art;
use App\Entity\Main\CopyFou;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NomDecisionnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nom', TextType::class,[
                'attr' => ['class' => 'form-control col-12 mb-2',
                'placeholder' => 'Le nom de votre Décisionnel'],
                'label_attr' => ['class' => 'd-none'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un nom poru votre decisionnel'
                    ])
                    ],
                    'required' => true,
            ])
            ->add('fournisseurs', EntityType::class, [
                'class' => CopyFou::class,
                'label' => false,
                'required' => true,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded'=>false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'select2 form-control col-12 m-2',
                    'data-placeholder' => 'Selectionnez les fournisseurs'
                ],
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('f')
                            ->where('f.closedAt is null')
                            ->orderBy('f.nom', 'ASC');
                },

                
            ])

            
            ->add('Creer', SubmitType::class,[
                'attr' => ['class' => 'btn btn-success m-2 col-3 float-right'],
                'label' => 'Créer un nouveau décisionnel'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            
        ]);
    }
}
