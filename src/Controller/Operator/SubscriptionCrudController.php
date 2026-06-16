<?php

namespace App\Controller\Operator;

use App\Entity\Control\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class SubscriptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subscription::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Abonnement')
            ->setEntityLabelInPlural('Abonnements')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('tenant', 'Locataire');
        yield AssociationField::new('plan', 'Plan');
        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'Essai' => Subscription::STATUS_TRIAL,
                'Actif' => Subscription::STATUS_ACTIVE,
                'Impayé' => Subscription::STATUS_PAST_DUE,
                'Annulé' => Subscription::STATUS_CANCELED,
            ])
            ->renderAsBadges([
                Subscription::STATUS_TRIAL => 'info',
                Subscription::STATUS_ACTIVE => 'success',
                Subscription::STATUS_PAST_DUE => 'warning',
                Subscription::STATUS_CANCELED => 'danger',
            ]);
        yield DateTimeField::new('trialEndsAt', 'Fin d\'essai');
        yield DateTimeField::new('currentPeriodEnd', 'Fin de période');
        yield TextField::new('provider', 'Fournisseur')->hideOnIndex();
        yield TextField::new('providerRef', 'Réf. fournisseur')->hideOnIndex();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices([
                'Essai' => Subscription::STATUS_TRIAL,
                'Actif' => Subscription::STATUS_ACTIVE,
                'Impayé' => Subscription::STATUS_PAST_DUE,
                'Annulé' => Subscription::STATUS_CANCELED,
            ]));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
