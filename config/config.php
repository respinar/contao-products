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
		'categories' => array
		(
			'tables'     => array('tl_product_category'),
			'icon'       => 'system/modules/products/assets/category.png',
		),
		/*'prices' => array
		(
			'tables'     => array('tl_product_price_category','tl_product_price'),
			'icon'       => 'system/modules/products/assets/price.png',
		),*/
	),	
));

/**
 * Front end modules
 */

array_insert($GLOBALS['FE_MOD'], 2, array
(
	'products' => array
	(
		'product_list'    => 'ModuleProductList',
		'product_detail'  => 'ModuleProductDetail',
	)
));


/**
 * Content elements
 */

array_insert($GLOBALS['TL_CTE'], 2, array
(
	'products' => array
	(
		'product'    => 'ContentProduct',
	)
));


/**
 * Register hook to add carpets items to the indexer
 */
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('Product', 'getSearchablePages');


// Registrieren im Hooks replaceInsertTags
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Product', 'productURLInsertTags');


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
