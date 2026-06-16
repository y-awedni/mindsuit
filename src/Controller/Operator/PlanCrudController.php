<?php

namespace App\Controller\Operator;

use App\Entity\Control\Plan;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plan::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Plan')
            ->setEntityLabelInPlural('Plans')
            ->setDefaultSort(['sortOrder' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('code', 'Code');
        yield TextField::new('name', 'Nom');
        yield MoneyField::new('priceMonthly', 'Prix mensuel (millimes)')
            ->setCurrency('TND')
            ->setStoredAsCents(false)
            ->setNumDecimals(0);
        yield MoneyField::new('priceYearly', 'Prix annuel (millimes)')
            ->setCurrency('TND')
            ->setStoredAsCents(false)
            ->setNumDecimals(0);
        yield BooleanField::new('active', 'Actif');
        yield IntegerField::new('sortOrder', 'Ordre');
        yield ArrayField::new('limits', 'Limites')
            ->setHelp('Clés : docsPerMonth, users. Valeur null = illimité.');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
