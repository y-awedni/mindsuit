<?php

namespace App\Controller;

use App\Service\ExcelCustomConfig;
use App\Service\ExcelFactory;
use App\Service\FormatDate;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'app.format_date' => FormatDate::class,
            'app.excel_custom_config' => ExcelCustomConfig::class,
            'phpexcel' => ExcelFactory::class,
            'knp_paginator' => PaginatorInterface::class,
            'translator' => TranslatorInterface::class,
            'doctrine' => ManagerRegistry::class,
            'request_stack' => RequestStack::class,
            'session' => '?'.SessionInterface::class,
        ]);
    }

    protected function getDoctrine(): ManagerRegistry
    {
        return $this->container->get('doctrine');
    }

    protected function get(string $id): object
    {
        if ($id === 'session') {
            return $this->container->get('request_stack')->getSession();
        }

        return $this->container->get($id);
    }

    protected function getEm(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }

    protected function societeLogoPath($societe): ?string
    {
        if (!$societe || !$societe->getMedia()) {
            return null;
        }

        return $this->getParameter('kernel.project_dir') . '/public/' . $societe->getMedia()->getAssetPath();
    }
}
