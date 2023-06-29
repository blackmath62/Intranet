<?php

namespace App\Form\Main;

use App\Entity\Main\SignatureElectronique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignatureElectroniqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('signatureId')
            ->add('documentId')
            ->add('signerId')
            ->add('pdfSansSignature')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SignatureElectronique::class,
        ]);
    }
}
