<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class StockType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('qte')
                ->add('note')
                ->add('article', EntityType::class, [
                    'empty_data' => null,
                    'required' => false,
                    'class' => 'App\\Entity\\Article',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                                ->where('a.service=false');
                    },
                    'attr' => ['class' => 'selectpicker', 'data-live-search' => true]
                ])
                ->add('dateCreation', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format'=>'dd/MM/yyyy',
                    'required'=>false,
                    'attr'=>array('class'=>'datepicker')
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Stock'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_stock';
    }

}
