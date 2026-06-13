<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LigneReglementBonReceptionType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('dateReglement', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array('class' => 'datepicker')
                ))
                ->add('montant', MoneyType::class, ['required' => true, 'currency' => 'TND', 'scale' => 3])
                ->add('modeReglement', ChoiceType::class, array(
                    'choices' => array(
                        'Espéce' => 'Espéce',
                        'Chéque' => 'Chéque',
                        'Traite' => 'Traite',
                        'Virement' => 'Virement',
                    ),
                ))
                ->add('dateEcheanceCheque', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array('class' => 'datepicker')
                ))
                ->add('numCheque')
                ->add('dateEcheanceTraite', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array('class' => 'datepicker')
                ))
                ->add('numTraite')
                ->add('compte', EntityType::class, array(
                    'empty_data'  => null,
                    
                    'required' => false,
                    // query choices from this entity
                    'class' => 'AppBundle:Compte',
                    // use the User.username property as the visible option string
                    'label' => 'CompteEmetteur'
                ))
                ->add('note', TextareaType::class, ['required' => false, 'trim' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\LigneReglementBonReception'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_lignereglementbonreception';
    }

}
