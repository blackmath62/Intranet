<?php

namespace App\Form;

use App\Entity\Main\Chats;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un commentaire',
                    ]),
                ],
                'required' => true,
                'label_attr' => ['class' => 'd-none'],
            ])
            ->add('Envoyer', SubmitType::class, [
                'label' => "Envoyer",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chats::class,
        ]);
    }
}
