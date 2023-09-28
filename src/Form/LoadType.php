<?php

namespace App\Form;

use App\Entity\Divalto\Art;
use App\Entity\Divalto\Fou;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fournisseurs', EntityType::class, [
                'label' => false,
                'choice_label' => 'tiers',
                'multiple' => true,
                'expanded' => false,
                'class' => Fou::class,
                'attr' => [
                    'class' => 'select2 col-12 btn-dark',
                    'data-placeholder' => 'Selectionnez les fournisseurs',
                ]]
            )
            ->add('Charger', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark'],
            ])
        ;

        $formModifier = function (FormInterface $form, Fou $fournisseurs = null) {
            $articles = (null === $fournisseurs) ? [] : $fournisseurs->getTiers();

            $form->add('articles', EntityType::class, [
                'class' => Art::class,
                'label' => false,
                'choice_label' => 'des',
                'multiple' => true,
                'expanded' => false,
                'choices' => $articles,

                'attr' => [
                    'class' => 'select2 col-12 btn-dark',
                    'data-placeholder' => 'Selectionnez les articles',
                ]]
            );
        };
        $builder->get('fournisseurs')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $fournisseurs = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $fournisseurs);
            }
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fou::class,
        ]);
    }
}
