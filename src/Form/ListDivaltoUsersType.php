<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use App\Entity\Main\ListDivaltoUsers;
use Symfony\Component\Form\AbstractType;
use App\Entity\Main\UsersDivaltoByFunction;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ListDivaltoUsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('users', EntityType::class, [
                'class' => ListDivaltoUsers::class,
                'label' => false,
                'required' => true,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded'=>true,
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-control col-12 m-2',
                    'data-placeholder' => 'Selectionnez les fournisseurs'
                ],
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('f')
                            ->where('f.valid = true');
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
            'data_class' => UsersDivaltoByFunction::class,
        ]);
    }
}
