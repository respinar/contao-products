<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Respinar\ProductsBundle\RespinarProductsBundle;
use Terminal42\ChangeLanguage\Terminal42ChangeLanguageBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(RespinarProductsBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    Terminal42ChangeLanguageBundle::class,
                ]),
        ];
    }
}
