<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LigneDevisType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('article',EntityType::class,[
                    'empty_data'  => null,
                    'required' => false,
                    'class' => 'AppBundle:Article',
                    'attr' => [ 
                        'class' => 'selectpicker',
                        'data-live-search' => true
                        ]
                    ])
                ->add('designation', TextareaType::class, ['required' => true, 'trim' => true])
                ->add('qte')
                ->add('prixUnitaire', MoneyType::class, ['required' => true, 'currency' => 'TND', 'scale' => 3])
                ->add('remise', PercentType::class, ['required' => true,'type' => 'integer'])
                ->add('tva', EntityType::class, ['class' => 'AppBundle:Tva', 'choice_label' => 'taux', 'choice_value' => 'taux'])
                ->add('ttc', MoneyType::class, ['required' => false, 'currency' => 'TND', 'scale' => 3])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\LigneDevis'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_lignedevis';
    }

}
