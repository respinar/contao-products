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

use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\ProductCatalogModel;

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['products'] = array(
		'products' => array(
			'tables' => array('tl_product_catalog', 'tl_product', 'tl_content')
			)
		);

/**
 * Register models
 */
 $GLOBALS['TL_MODELS']['tl_product']         = ProductModel::class;
 $GLOBALS['TL_MODELS']['tl_product_catalog'] = ProductCatalogModel::class; 

/**
 * Register hook to add carpets items to the indexer
 */
//$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('Respinar\Products\Product', 'getSearchablePages');

// Registrieren im Hooks replaceInsertTags
//$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Respinar\Products\Product', 'productURLInsertTags');

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'products';
$GLOBALS['TL_PERMISSIONS'][] = 'productp';
