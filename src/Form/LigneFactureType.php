<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class LigneFactureType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $form->add('article', EntityType::class, [
                'empty_data' => null,
                'required' => false,
                'class' => 'App\\Entity\\Article',
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true
                ]
            ]);
        });
        $builder
                ->add('designation', TextareaType::class, ['required' => true, 'trim' => true])
                ->add('qte')
                ->add('prixUnitaire', MoneyType::class, ['grouping' => false, 'required' => true, 'currency' => 'TND', 'scale' => 3])
                ->add('remise', PercentType::class, ['required' => true, 'type' => 'integer'])
                ->add('tva', EntityType::class, ['class' => 'App\\Entity\\Tva', 'choice_label' => 'taux', 'choice_value' => 'taux'])
                ->add('ttc', MoneyType::class, ['grouping' => false, 'required' => false, 'currency' => 'TND', 'scale' => 3])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\LigneFacture'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_lignefacture';
    }

}
