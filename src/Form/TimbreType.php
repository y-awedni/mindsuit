<?php

namespace App\Form;

use App\Entity\Timbre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimbreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('valeur', NumberType::class, [
            'label' => 'Valeur du timbre fiscal',
            'scale' => 3,
            'html5' => true,
            'attr' => ['step' => '0.001', 'min' => '0'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Timbre::class,
        ]);
    }
}
