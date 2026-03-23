<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\ArrayUtil;
use Respinar\ProductsBundle\Model\CatalogModel;
use Respinar\ProductsBundle\Model\ProductModel;

/*
 * Back end modules
 */
ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 1, [
    'products' => [
        'products' => [
            'tables' => ['tl_product_catalog', 'tl_product', 'tl_content'],
        ],
    ],
]);

/*
 * Register models
 */
$GLOBALS['TL_MODELS']['tl_product'] = ProductModel::class;
$GLOBALS['TL_MODELS']['tl_product_catalog'] = CatalogModel::class;

/*
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'products';
