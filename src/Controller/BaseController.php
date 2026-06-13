<?php

namespace App\Controller;

use App\Service\ExcelCustomConfig;
use App\Service\ExcelFactory;
use App\Service\FormatDate;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Base controller exposing, through the controller service locator, the few
 * application services the legacy controllers still reach via $this->get(...).
 * Replaces the removed Symfony\Bundle\FrameworkBundle\Controller\Controller.
 */
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
        ]);
    }
}
