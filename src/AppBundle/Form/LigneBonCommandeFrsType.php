<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class LigneBonCommandeFrsType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('article',EntityType::class,[
        	'empty_data'  => null,
                'required' => false,
                'class' => 'AppBundle:Article',
        	'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                                ->where('a.service=false');
                    },
        	'attr' => ['class' => 'selectpicker', 'data-live-search' => true]
        	])
                ->add('designation',TextareaType::class,['required' => true,'trim'=>true])
                ->add('qte');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\LigneBonCommandeFrs'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_ligneboncommandefrs';
    }

}
