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
	'Respinar\Products',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'Respinar\Products\ModuleProduct'       => 'system/modules/products/library/Respinar/Products/FrontendModule/ModuleProduct.php',
	'Respinar\Products\ModuleProductDetail' => 'system/modules/products/library/Respinar/Products/FrontendModule/ModuleProductDetail.php',
	'Respinar\Products\ModuleProductList'   => 'system/modules/products/library/Respinar/Products/FrontendModule/ModuleProductList.php',

	// Models
	'Respinar\Products\ProductPriceModel'   => 'system/modules/products/library/Respinar/Products/Model/ProductPriceModel.php',
	'Respinar\Products\ProductModel'        => 'system/modules/products/library/Respinar/Products/Model/ProductModel.php',
	'Respinar\Products\ProductCatalogModel' => 'system/modules/products/library/Respinar/Products/Model/ProductCatalogModel.php',

	// Classes
	'Respinar\Products\Product'             => 'system/modules/products/library/Respinar/Products/Product.php',

	// Change Language Classes
	'Respinar\Products\EventListener\CallbackSetupListener' => 'system/modules/products/library/Respinar/Products/EventListener/CallbackSetupListener.php',
	'Respinar\Products\EventListener\Navigation\ProductNavigationListener' => 'system/modules/products/library/Respinar/Products/EventListener/Navigation/ProductNavigationListener.php',
	'Respinar\Products\EventListener\DataContainer\ProductListener' => 'system/modules/products/library/Respinar/Products/EventListener/DataContainer/ProductListener.php'
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
