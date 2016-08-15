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
	// Modules
	'product\ModuleProduct'       => 'system/modules/products/modules/ModuleProduct.php',
	'product\ModuleProductDetail' => 'system/modules/products/modules/ModuleProductDetail.php',
	'product\ModuleProductList'   => 'system/modules/products/modules/ModuleProductList.php',

	// Models
	'product\ProductPriceModel'   => 'system/modules/products/models/ProductPriceModel.php',
	'product\ProductModel'        => 'system/modules/products/models/ProductModel.php',
	'product\ProductCatalogModel' => 'system/modules/products/models/ProductCatalogModel.php',

	// Classes
	'product\Product'             => 'system/modules/products/classes/Product.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_product_detail' => 'system/modules/products/templates/modules',
	'mod_product_list'   => 'system/modules/products/templates/modules',
	'product_short'      => 'system/modules/products/templates/product',
	'product_full'       => 'system/modules/products/templates/product',
	'related_short'      => 'system/modules/products/templates/related',
));
