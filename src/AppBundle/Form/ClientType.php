<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;

class ClientType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('code')
                ->add('status', ChoiceType::class, array(
                    'choices' => array(
                        'Client' => 'Client',
                        'Prospect' => 'Prospect',
                    ),
                ))
                ->add('civilite', ChoiceType::class, array(
                    'attr' => ['class' => 'selectpicker', 'data-live-search' => true],
                    'choices' => array(
                        'empty' => '',
                        'Auto-entrepreneur' => 'Auto-entrepreneur',
                        'Micro-entrepreneur' => 'Micro-entrepreneur',
                        'M.' => 'M.',
                        'Mme' => 'Mme',
                        'Mlle' => 'Mlle',
                        'SA' => 'SA',
                        'SAS' => 'SAS',
                        'EURL' => 'EURL',
                        'EARL' => 'EARL',
                        'SARL' => 'SARL',
                        'SPRL' => 'SPRL',
                        'SCI' => 'SCI',
                        'EI' => 'EI',
                        'EIRL' => 'EIRL',
                        'Association' => 'Association'
                    ),
                    'required' => false
                ))
                ->add('rs')
                ->add('mf')
                ->add('rc')
                ->add('nom')
                ->add('prenom')
                ->add('adresse1')
                ->add('adresse2')
                ->add('adresse3')
                ->add('codePostal')
                ->add('ville')
                ->add('pays')
                ->add('tel')
                ->add('mobile')
                ->add('fax')
                ->add('email')
                ->add('siteWeb')
                ->add('activite')
                ->add('remise', PercentType::class, ['required' => false, 'scale' => 3, 'type' => 'integer'])
                ->add('note')
                ->add('save', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
                    'label' => 'Save', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Client'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_client';
    }

}
