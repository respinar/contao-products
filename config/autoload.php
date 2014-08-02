<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Products
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
	// Models
	'catalog\CatalogTypeModel'     => 'system/modules/products/models/CatalogTypeModel.php',
	'catalog\CatalogProductModel'  => 'system/modules/products/models/CatalogProductModel.php',
	'catalog\CatalogCategoryModel' => 'system/modules/products/models/CatalogCategoryModel.php',
	'catalog\CatalogModel'         => 'system/modules/products/models/CatalogModel.php',

	// Modules
	'catalog\ModuleCatalogProduct' => 'system/modules/products/modules/ModuleCatalogProduct.php',
	'catalog\ModuleCatalogList'    => 'system/modules/products/modules/ModuleCatalogList.php',
	'catalog\ModuleCatalogRelated' => 'system/modules/products/modules/ModuleCatalogRelated.php',
	'catalog\ModuleCatalogMenu'    => 'system/modules/products/modules/ModuleCatalogMenu.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_catalog_related' => 'system/modules/products/templates/modules',
	'mod_catalog_product' => 'system/modules/products/templates/modules',
	'mod_catalog_list'    => 'system/modules/products/templates/modules',
	'mod_catalog_menu'    => 'system/modules/products/templates/modules',
	'mod_catalog_empty'   => 'system/modules/products/templates/modules',
));
