<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Form\LigneDevisType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DevisType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('code', TextType::class, ['attr'=>['readonly'=>true]])
                ->add('client',EntityType::class, ['attr'=>['class'=>'selectpicker','data-live-search'=>true,'title'=>'Chercher et sélectionner'],'class' => 'AppBundle:Client'])
                ->add('cin')
                ->add('nom')
                ->add('note')
                ->add('dateCreation', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format'=>'dd/MM/yyyy',
                    'required'=>false,
                    'attr'=>array('class'=>'datepicker')
                ))
                ->add('dateValidite', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format'=>'dd/MM/yyyy',
                    'required'=>false,
                    'attr'=>array('class'=>'datepicker')
                ));
        $builder->add('lignesDevis', CollectionType::class, array(
            'entry_type' => LigneDevisType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'mapped' => true,
            'by_reference' => false
        ));
        $builder->add('ht', HiddenType::class, array());
        $builder->add('remise', HiddenType::class, array());
        $builder->add('tva', HiddenType::class, array());
        $builder->add('total', HiddenType::class, array());
        $builder->add('save', SubmitType::class, array('label' => 'Save','attr' => array('class' => 'btn-success fa fa-save btn-lg')));
        $builder->add('saveAndPrint', SubmitType::class, array('label' => 'Save and Print','attr' => array('class' => 'btn-success fa fa-save btn-lg')));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                    $devi = $event->getData();
                    if (!$devi) {
                        return;
                    }
                    foreach ($devi['lignesDevis'] as $key => $ligne) {
                        if ($devi['lignesDevis'][$key]['article'] === "") {
                            unset($devi['lignesDevis'][$key]);
                        }
                    }
                    $event->setData($devi);
                });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Devis'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_devis';
    }

}
