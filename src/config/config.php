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


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD'], 1, array
(
	'products' => array
	(
		'products' => array
		(
			'tables'     => array('tl_product_catalog','tl_product','tl_content'),
			'icon'       => 'system/modules/products/assets/product.png',
		),
		/*
		'categories' => array
		(
			'tables'     => array('tl_product_category'),
			'icon'       => 'system/modules/products/assets/category.png',
		),
		'prices' => array
		(
			'tables'     => array('tl_product_price_category','tl_product_price'),
			'icon'       => 'system/modules/products/assets/price.png',
		),*/
	),
));

/**
 * Register models
 */

 $GLOBALS['TL_MODELS']['tl_product']         = 'Respinar\Products\Model\ProductModel';
 $GLOBALS['TL_MODELS']['tl_product_catalog'] = 'Respinar\Products\Model\ProductCatalogModel'; 
 $GLOBALS['TL_MODELS']['tl_product_price']   = 'Respinar\Products\Model\ProductPriceModel'; 

/**
 * Front end modules
 */

array_insert($GLOBALS['FE_MOD'], 2, array
(
	'products' => array
	(
		'product_list'    => 'Respinar\Products\Frontend\Module\ModuleProductList',
		'product_detail'  => 'Respinar\Products\Frontend\Module\ModuleProductDetail',
	)
));


/**
 * Content elements
 */
$GLOBALS['TL_CTE']['miscellaneous']['product']   = 'Respinar\Products\Frontend\Element\ContentProductSingle';


/**
 * Register hook to add carpets items to the indexer
 */
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('Respinar\Products\Product', 'getSearchablePages');


// Registrieren im Hooks replaceInsertTags
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Respinar\Products\Product', 'productURLInsertTags');


/**
 * Hooks
 */


$GLOBALS['TL_HOOKS']['loadDataContainer'][] = [
	'Respinar\Products\EventListener\CallbackSetupListener',
	'onLoadDataContainer'
];

$GLOBALS['TL_HOOKS']['changelanguageNavigation'][] = [
    'Respinar\Products\EventListener\Navigation\ProductNavigationListener',
    'onChangelanguageNavigation'
];

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'products';
$GLOBALS['TL_PERMISSIONS'][] = 'productp';
