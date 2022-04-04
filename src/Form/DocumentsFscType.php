<?php

namespace App\Form;

use App\Entity\Main\documentsFsc;
use App\Entity\Main\TypeDocumentFsc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DocumentsFscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control col-12 text-center']
            ])
            ->add('typeDoc',EntityType::class,[
                'class' => TypeDocumentFsc::class,
                'choice_label' => 'title',
                'mapped' => false,
                'placeholder' => 'Veuillez selectionner un type de document Fsc',
                'choice_name' => 'id',
                'expanded' => false,
                'required' => true,
                'multiple' => false,
                'label' => 'Type de document Fsc',
                'attr' => ['class' => 'mr-3 form-control col-12 col-sm-12 text-center mb-3'],
                'label_attr' => ['class' => 'col-12 col-sm-12 text-center mt-3'] 
                ])
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => documentsFsc::class,
        ]);
    }
}
