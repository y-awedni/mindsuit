<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CompteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('rub')
                ->add('banque',EntityType::class, ['attr'=>['class'=>'selectpicker','data-live-search'=>true,'title'=>'Chercher et sélectionner'],'class' => 'App\\Entity\\Banque']);
        $builder->add('save', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class,array(
            'label' => 'Save', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Compte'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_compte';
    }


}
