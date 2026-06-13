<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MouvementType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('designation', TextType::class, array('label' => 'RefDoc', 'attr' => ['required' => true]))
                ->add('typeDoc', ChoiceType::class, array(
                    'choices' => array(
                        'Reçu' => 'recu',
                        'Bon de reception' => 'br',
                        'Bon de livraison' => 'bl',
                        'Facture' => 'facture'
                    ),
                    'required' => true
                ))
                ->add('ttc', MoneyType::class, ['required' => true, 'currency' => 'TND', 'scale' => 3])
                ->add('modeReglement', ChoiceType::class, array(
                    'choices' => array(
                        'Espéce' => 'Espéce',
                        'Chéque' => 'Chéque',
                        'Traite' => 'Traite',
                        'Virement' => 'Virement'
                    )
                ))
                ->add('totalRetenu', MoneyType::class, array(
                    'currency' => 'TND',
                    'scale' => 3,
                    'attr' => array(
                        'readonly' => true
                    )
                ))
                ->add('dateCreation', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array('class' => 'datepicker')
                ))
                ->add('dateEcheance', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array('class' => 'datepicker')
                ))
                ->add('note', TextareaType::class, array('attr' => ['required' => false]))
                ->add('tier', TextType::class, array('attr' => ['required' => true]))
                ->add('save', SubmitType::class, array(
                    'label' => 'Save', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Mouvement'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_mouvement';
    }

}
