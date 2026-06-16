<?php

namespace App\Controller\Operator;

use App\Entity\Control\Tenant;
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

class TenantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tenant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Locataire')
            ->setEntityLabelInPlural('Locataires')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('subdomain', 'Sous-domaine');
        yield TextField::new('companyName', 'Société');
        yield TextField::new('dbName', 'Base de données')->hideOnForm();
        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'Essai' => Tenant::STATUS_TRIAL,
                'Actif' => Tenant::STATUS_ACTIVE,
                'Suspendu' => Tenant::STATUS_SUSPENDED,
            ])
            ->renderAsBadges([
                Tenant::STATUS_TRIAL => 'info',
                Tenant::STATUS_ACTIVE => 'success',
                Tenant::STATUS_SUSPENDED => 'danger',
            ]);
        yield AssociationField::new('plan', 'Plan');
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices([
                'Essai' => Tenant::STATUS_TRIAL,
                'Actif' => Tenant::STATUS_ACTIVE,
                'Suspendu' => Tenant::STATUS_SUSPENDED,
            ]));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
