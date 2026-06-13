<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Form\LigneFactureAvoirType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class FactureAvoirType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('code', TextType::class, ['attr' => ['readonly' => true]])
                ->add('note')
                ->add('dateCreation', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array('class' => 'datepicker')
                ))
                ->add('ligneFactureAvoirs', CollectionType::class, array(
                    'entry_type' => LigneFactureAvoirType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'mapped' => true,
                    'by_reference' => false
                ))
                ->add('termine')
                ->add('ht', HiddenType::class, array())
                ->add('remise', HiddenType::class, array())
                ->add('tva', HiddenType::class, array())
                ->add('total', HiddenType::class, array())
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $factureAvoir = $event->getData();
                    $form = $event->getForm();
                    $form->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')));
                    $form->add('saveAndPrint', SubmitType::class, array('label' => 'Save and Print', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')));
                })
                ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                    $entity = $event->getData();
                    if (!$entity) {
                        return;
                    }
                    foreach ($entity['ligneFactureAvoirs'] as $key => $ligne) {
                        if ($entity['ligneFactureAvoirs'][$key]['article'] === "") {
                            unset($entity['ligneFactureAvoirs'][$key]);
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
            'data_class' => 'AppBundle\Entity\FactureAvoir'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_factureavoir';
    }

}
