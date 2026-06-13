<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\MediaType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Famille;
use App\Entity\Categorie;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ArticleType extends AbstractType {

    private function addSousFamilleField(FormInterface $form, Famille $famille = null) {
        $form->add('sousfamille', EntityType::class, [
            'attr' => [
                'class' => 'selectpicker', 
                'data-live-search' => true, 
                'title' => 'Chercher et sélectionner'
                ], 
            'class' => 'App\Entity\Sousfamille',
            'placeholder' => $famille ? 'Sélectionnez sous famille' : 'Sélectionnez famille',
            'choices' => $famille ? $famille->getSousFamilles() : []
        ]);
    }

    private function addFamilleField(FormInterface $form, Categorie $categorie = null) {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder('famille', EntityType::class, null, [
            'attr' => [
                'class' => 'selectpicker', 
                'data-live-search' => true, 
                'title' => 'Chercher et sélectionner'
                ], 
            'class' => 'App\Entity\Famille',
            'placeholder' => $categorie ? 'Sélectionnez votre famille' : 'Sélectionnez votre catégorie',
            'mapped' => false,
            'required' => false,
            'auto_initialize' => false,
            'choices' => $categorie ? $categorie->getFamilles() : []
        ]);
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $this->addSousFamilleField($form->getParent(), $form->getData());
        });
        $form->add($builder->getForm());
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('code');
        $builder->add('designation', TextareaType::class, array('required' => false));
        $builder->add('unite');
        $builder->add('prixAchat', MoneyType::class, ['required' => false, 'currency' => 'TND', 'scale' => 3]);
        $builder->add('marge', PercentType::class, ['required' => false, 'type' => 'integer']);
        $builder->add('prixVenteHt', MoneyType::class, ['required' => false, 'currency' => 'TND', 'scale' => 3]);
        $builder->add('tva', EntityType::class, ['attr' => ['class' => 'selectpicker', 'data-live-search' => true, 'title' => 'Chercher et sélectionner'], 'class' => 'App\\Entity\\Tva', 'choice_label' => 'taux', 'choice_value' => 'taux']);
        $builder->add('note');
        $builder->add('desactiverPhoto');
        $builder->add('media', MediaType::class);
        $builder->add('save', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));

        $builder->add('categorie', EntityType::class, [
            'attr' => [
                'class' => 'selectpicker', 
                'data-live-search' => true, 
                'title' => 'Chercher et sélectionner'
                ], 
            'class' => 'App\\Entity\\Categorie'
            ]);

        $builder->get('categorie')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $this->addFamilleField($form->getParent(), $form->getData());
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData(); /* @var $ville Ville */
            $sousFamille = $data->getSousfamille();
            $form = $event->getForm();
            if ($sousFamille) {
                // On récupère le famille et la categorie
                $famille = $sousFamille->getFamille();
                $categorie = $famille->getCategorie();
                // On crée les 2 champs supplémentaires
                $this->addFamilleField($form, $categorie);
                $this->addSousFamilleField($form, $famille);
                // On set les données
                $form->get('categorie')->setData($categorie);
                $form->get('famille')->setData($famille);
            } else {
                // On crée les 2 champs en les laissant vide (champs utilisé pour le JavaScript)
                $this->addFamilleField($form, null);
                $this->addSousFamilleField($form, null);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Article',
            'validation_groups' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_article';
    }

}
