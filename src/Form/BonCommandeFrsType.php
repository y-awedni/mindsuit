<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class BonCommandeFrsType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('code', TextType::class, ['attr' => ['readonly' => true]])
                ->add('note')
                ->add('dateCreation', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'attr' => array('class' => 'datepicker')
                ))
                ->add('dateCommande', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'attr' => array('class' => 'datepicker')
                ))
                ->add('fournisseur', EntityType::class, ['attr' => ['required' => true, 'class' => 'selectpicker', 'data-live-search' => true, 'title' => 'Chercher et sélectionner'], 'class' => 'App\\Entity\\Fournisseur'])
                ->add('ligneBonCommandeFrss', CollectionType::class, array(
                    'entry_type' => LigneBonCommandeFrsType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'mapped' => true,
                    'by_reference' => false
                ))
                ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')))
                ->add('saveAndPrint', SubmitType::class, array('label' => 'Save and Print', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')))
                ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                    $entity = $event->getData();
                    if (!$entity) {
                        return;
                    }
                    foreach ($entity['ligneBonCommandeFrss'] as $key => $ligne) {
                        if ($entity['ligneBonCommandeFrss'][$key]['article'] === "") {
                            unset($entity['ligneBonCommandeFrss'][$key]);
                        }
                    }
                    $event->setData($entity);
                });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\BonCommandeFrs'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_boncommandefrs';
    }

}
