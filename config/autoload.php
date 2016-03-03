<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'product',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'product\Product'             => 'system/modules/product/classes/Product.php',

	// Models
	'product\ProductCatalogModel' => 'system/modules/product/models/ProductCatalogModel.php',
	'product\ProductModel'        => 'system/modules/product/models/ProductModel.php',
	'product\ProductPriceModel'   => 'system/modules/product/models/ProductPriceModel.php',

	// Modules
	'product\ModuleProductList'   => 'system/modules/product/modules/ModuleProductList.php',
	'product\ModuleProductDetail' => 'system/modules/product/modules/ModuleProductDetail.php',
	'product\ModuleProduct'       => 'system/modules/product/modules/ModuleProduct.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_product_detail' => 'system/modules/product/templates/modules',
	'mod_product_list'   => 'system/modules/product/templates/modules',
	'product_full'       => 'system/modules/product/templates/product',
	'product_short'      => 'system/modules/product/templates/product',
	'related_short'      => 'system/modules/product/templates/related',
));
