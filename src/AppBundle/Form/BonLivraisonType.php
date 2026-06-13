<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Form\LigneBonLivraisonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BonLivraisonType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('code', TextType::class, ['attr' => ['readonly' => true]])
                ->add('client',EntityType::class, ['attr'=>['required'=>true, 'class'=>'selectpicker','data-live-search'=>true,'title'=>'Chercher et sélectionner'],'class' => 'AppBundle:Client'])
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
                ->add('dateLivraison', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format'=>'dd/MM/yyyy',
                    'required'=>false,
                    'attr'=>array('class'=>'datepicker')
        ));
        $builder->add('ligneBonLivraisons', CollectionType::class, array(
            'entry_type' => LigneBonLivraisonType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'mapped' => true,
            'by_reference' => false
        ));
        $builder->add('termine');
        $builder->add('ht', HiddenType::class, array());
        $builder->add('remise', HiddenType::class, array());
        $builder->add('tva', HiddenType::class, array());
        $builder->add('total', HiddenType::class, array());
        $builder->add('tauxRetenu', HiddenType::class, array());
        $builder->add('totalRetenu', HiddenType::class, array());
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $bonLivraison = $event->getData();
            $form = $event->getForm();
            if (!$bonLivraison) {
                return;
            }
            if (!$bonLivraison->getId() or $bonLivraison->getRegle()==0) {
                $form->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')));
                $form->add('saveAndPrint', SubmitType::class, array('label' => 'Save and Print', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')));
            }
        })
        ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                    $entity= $event->getData();
                    if (!$entity) {
                        return;
                    }
                    foreach ($entity['ligneBonLivraisons'] as $key => $ligne) {
                        if ($entity['ligneBonLivraisons'][$key]['article'] === "") {
                            unset($entity['ligneBonLivraisons'][$key]);
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
            'data_class' => 'AppBundle\Entity\BonLivraison'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_bonlivraison';
    }

}
