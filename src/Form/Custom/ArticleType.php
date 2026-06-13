<?php

namespace App\Form\Custom;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ArticleType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('code')
                ->add('designation', TextareaType::class, array('required' => false))
                ->add('service', CheckboxType::class, array(
                    'label' => 'Service ?',
                    'required' => false
                ))
                ->add('stockable', CheckboxType::class, array(
                    'label' => 'Stockable ?',
                    'required' => false
                ))
                ->add('fournisseur', EntityType::class, [
                    'attr' => ['class' => 'selectpicker', 'data-live-search' => true, 'title' => 'Chercher et sélectionner'], 'class' => 'App\\Entity\\Fournisseur'
                ])
                ->add('prixAchat', MoneyType::class, ['required' => false, 'currency' => 'TND', 'scale' => 3])
                ->add('qteEnDepart')
                ->add('qteEnStock', TextType::class, ['attr' => ['readonly' => true]])
                ->add('seuilAlert')
                ->add('dateAjout', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array('class' => 'datepicker'),
                    'data' => new \DateTime()
                ))
                ->add('save', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
                    'label' => 'Save', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_article';
    }

}
