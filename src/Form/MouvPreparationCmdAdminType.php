<?php

namespace App\Form;

use App\Entity\Main\MouvPreparationCmdAdmin;
use App\Entity\Main\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MouvPreparationCmdAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('preparedBy', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_LOGISTIQUE_PREPARATEUR%')
                        ->orderBy('u.id', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MouvPreparationCmdAdmin::class,
        ]);
    }
}
