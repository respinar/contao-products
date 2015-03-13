<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package Catalog
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'catalog',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'catalog\ModuleCatalogDetail'  => 'system/modules/catalog/modules/ModuleCatalogDetail.php',
	'catalog\ModuleCatalog'        => 'system/modules/catalog/modules/ModuleCatalog.php',
	'catalog\ModuleCatalogList'    => 'system/modules/catalog/modules/ModuleCatalogList.php',

	// Models
	'catalog\CatalogProductModel'  => 'system/modules/catalog/models/CatalogProductModel.php',
	'catalog\CatalogCategoryModel' => 'system/modules/catalog/models/CatalogCategoryModel.php',
	'catalog\CatalogTypeModel'     => 'system/modules/catalog/models/CatalogTypeModel.php',

	// Classes
	'catalog\Catalog'              => 'system/modules/catalog/classes/Catalog.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_catalog_list'   => 'system/modules/catalog/templates/modules',
	'mod_catalog_detail' => 'system/modules/catalog/templates/modules',
	'product_list'       => 'system/modules/catalog/templates/product',
	'product_full'       => 'system/modules/catalog/templates/product',
	'product_short'      => 'system/modules/catalog/templates/product',
	'product_type'       => 'system/modules/catalog/templates/type',
	'product_related'    => 'system/modules/catalog/templates/related',
));
