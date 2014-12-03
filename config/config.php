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
		'tables'     => array('tl_catalog','tl_catalog_product','tl_catalog_type','tl_content'),
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
		'catalog_related' => 'ModuleCatalogRelated',
	)
));
