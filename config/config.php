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
		'product' => array
		(
			'tables'     => array('tl_product_catalog','tl_product','tl_content'),
			'icon'       => 'system/modules/product/assets/icon.png',
		),
		//'prices' => array
		//(
		//	'tables'     => array('tl_product_price_category','tl_product_price'),
		//	'icon'       => 'system/modules/product/assets/price.png',
		//),		
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
 * Register hook to add carpets items to the indexer
 */
$GLOBALS['TL_HOOKS']['getSearchablePages'][]     = array('Product', 'getSearchablePages');

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['translateUrlParameters'][] = array('Product', 'translateUrlParameters');

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'products';
$GLOBALS['TL_PERMISSIONS'][] = 'productp';
