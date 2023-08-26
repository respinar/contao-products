<?php

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2023 <hamid.peywasti@gmail.com>
 *
 * @license MIT
 */

use Contao\ArrayUtil;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\CatalogModel;

/**
 * Back end modules
 */
ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 1, [
    'products' => [
        'products' => [
            'tables' => ['tl_product_catalog', 'tl_product', 'tl_content'],
        ],
    ],
]);

/**
 * Register models
 */
 $GLOBALS['TL_MODELS']['tl_product']         = ProductModel::class;
 $GLOBALS['TL_MODELS']['tl_product_catalog'] = CatalogModel::class;

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'products';
$GLOBALS['TL_PERMISSIONS'][] = 'productp';
