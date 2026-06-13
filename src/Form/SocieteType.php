<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Form\MediaType;

class SocieteType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('rs')
                ->add('mf')
                ->add('rcs')
                ->add('adresse')
                ->add('ville')
                ->add('pays')
                ->add('codePostale')
                ->add('tel')
                ->add('fax')
                ->add('compte')
                ->add('mobile')
                ->add('desactiverPhoto')
                ->add('media', MediaType::class)
                ->add('email')
                ->add('siteWeb');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Societe'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_societe';
    }

}
