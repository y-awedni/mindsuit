<?php

namespace App\Controller\Operator;

use App\Entity\Control\AdminUser;
use App\Entity\Control\Owner;
use App\Entity\Control\Payment;
use App\Entity\Control\Plan;
use App\Entity\Control\Subscription;
use App\Entity\Control\Tenant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/", name="operator_dashboard")
     */
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(TenantCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Moudir Admin')
            ->setFaviconPath('bundles/app/favicon.png')
            ->setLocales(['fr']);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::section('Clients');
        yield MenuItem::linkToCrud('Espaces', 'fa fa-building', Tenant::class);
        yield MenuItem::linkToCrud('Responsables', 'fa fa-user', Owner::class);
        yield MenuItem::section('Abonnements');
        yield MenuItem::linkToCrud('Plans', 'fa fa-tags', Plan::class);
        yield MenuItem::linkToCrud('Abonnements', 'fa fa-credit-card', Subscription::class);
        yield MenuItem::linkToCrud('Paiements', 'fa fa-money', Payment::class);
        yield MenuItem::section('Système');
        yield MenuItem::linkToCrud('Administrateurs', 'fa fa-lock', AdminUser::class);
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
