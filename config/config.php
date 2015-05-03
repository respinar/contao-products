<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   catalog
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL
 * @copyright 2014
 */


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], 1, array
(
	'catalog' => array
	(
		'tables'     => array('tl_catalog_category','tl_catalog_product','tl_catalog_type','tl_catalog_price','tl_content'),
		'icon'       => 'system/modules/catalog/assets/icon.png',
	)
));

/**
 * Front end modules
 */

array_insert($GLOBALS['FE_MOD'], 2, array
(
	'catalog' => array
	(
		'catalog_list'    => 'ModuleCatalogList',
		'catalog_detail'  => 'ModuleCatalogDetail',
	)
));


/**
 * Register hook to add carpets items to the indexer
 */
$GLOBALS['TL_HOOKS']['getSearchablePages'][]     = array('Catalog', 'getSearchablePages');

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['translateUrlParameters'][] = array('Catalog', 'translateUrlParameters');

