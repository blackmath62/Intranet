<?php

namespace App\Form;

use App\Entity\Divalto\Cli;
use App\Entity\Divalto\Vrp;
use Proxies\__CG__\App\Entity\Divalto\Vrp as DivaltoVrp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ClientsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                /*->add('stat0002', ChoiceType::class,[
                    'choices' => [
                        'HP' => "HP",
                        'EV' => "EV",
                        'ME' => "ME",
                    ],
                    'choice_attr' => [
                        'HP' => ['class' => 'm-3'],
                        'EV' => ['class' => 'm-3'],
                        'ME' => ['class' => 'm-3']
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'label' => 'Selectionnez le secteur'
                   
                ])*/
                ->add('repr0001', ChoiceType::class,[
                    'choices' => [
                        'Mr LHERMITTE JEAN MICHEL 06.16.40.36.40' => "1",
                        'DESCHODT ALEX Port: 06.20.63.40.97' => "2",
                        'YVES MALMONTE 06.21.38.17.35' => "6",
                        'BRUNO GOVAERE 06.03.11.87.85' => "9",
                        'DUPIRE XAVIER 06.89.87.64.14' => "10",
                        'LELEU LAURENT 06.07.81.60.16' => "11",
                        'THOMAS WRAZIDLO 06.80.68.46.62' => "17",
                        'REMI VASSET 07.63.79.31.59 ' => "20",
                        'JEROME POCHET TEL: 07.63.04.40.26' => "15",
                    ],
                    'choice_attr' => [
                        'Mr LHERMITTE JEAN MICHEL 06.16.40.36.40' => ['class' => 'm-3'],
                        'DESCHODT ALEX Port: 06.20.63.40.97' => ['class' => 'm-3'],
                        'YVES MALMONTE 06.21.38.17.35' => ['class' => 'm-3'],
                        'BRUNO GOVAERE 06.03.11.87.85' => ['class' => 'm-3'],
                        'DUPIRE XAVIER 06.89.87.64.14' => ['class' => 'm-3'],
                        'LELEU LAURENT 06.07.81.60.16' => ['class' => 'm-3'],
                        'THOMAS WRAZIDLO 06.80.68.46.62' => ['class' => 'm-3'],
                        'REMI VASSET 07.63.79.31.59 ' => ['class' => 'm-3'],
                        'JEROME POCHET TEL: 07.63.04.40.26' => ['class' => 'm-3'],
                    ],
                    'expanded' => false,
                    'multiple' => false,
                    'label_attr' => ['class' => 'd-none'],
                    'label' => 'Selectionnez le commercial',
                    'attr' => ['class' => 'form-control'],
                   
                ])
                          
            ->add('Modifier', SubmitType::class,[
                'attr' => ['class' => 'btn btn-dark'],
                'label' => 'Afficher les clients'
            ])      
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cli::class,
        ]);
    }
}
