<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
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
	// Models
	'catalog\CatalogTypeModel'     => 'system/modules/catalog/models/CatalogTypeModel.php',
	'catalog\CatalogModel'         => 'system/modules/catalog/models/CatalogModel.php',
	'catalog\CatalogProductModel'  => 'system/modules/catalog/models/CatalogProductModel.php',
	'catalog\CatalogCategoryModel' => 'system/modules/catalog/models/CatalogCategoryModel.php',

	// Modules
	'catalog\ModuleCatalogList'    => 'system/modules/catalog/modules/ModuleCatalogList.php',
	'catalog\ModuleCatalogRelated' => 'system/modules/catalog/modules/ModuleCatalogRelated.php',
	'catalog\ModuleCatalogProduct' => 'system/modules/catalog/modules/ModuleCatalogProduct.php',
	'catalog\ModuleCatalogMenu'    => 'system/modules/catalog/modules/ModuleCatalogMenu.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_catalog_product' => 'system/modules/catalog/templates/modules',
	'mod_catalog_related' => 'system/modules/catalog/templates/modules',
	'mod_catalog_menu'    => 'system/modules/catalog/templates/modules',
	'mod_catalog_list'    => 'system/modules/catalog/templates/modules',
	'mod_catalog_empty'   => 'system/modules/catalog/templates/modules',
));
