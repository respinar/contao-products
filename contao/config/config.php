<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   product
 * @author    Hamid Abbaszadeh
 * @license   LGPL-3.0+
 * @copyright 2014-2016
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
