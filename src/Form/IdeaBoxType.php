<?php

namespace App\Form;

use App\Entity\Main\IdeaBox;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class IdeaBoxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sujet', TextType::class, [
            'required' => true,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Veuillez saisir un objet...'
            ]
        ])
            ->add('content', TextareaType::class,[
                'required' => true,
                'label' => 'Détail',
                'attr' =>[
                'placeholder' => 'Dans la section ******* du site intranet, je pense qu\'il serait intéréssant d\'ajouter  *******, La fonctionnalité dans la section ****** n\'est pas adaptée à nos besoins, il faudrait ******* '
                ]
                ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => IdeaBox::class,
        ]);
    }
}
