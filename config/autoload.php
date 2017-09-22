<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

 /**
 * Register PSR-0 namespaces
 */
 if (class_exists('NamespaceClassLoader')) {
    NamespaceClassLoader::add('Respinar\Products', 'system/modules/products/library');
}

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
	'ce_product'         => 'system/modules/products/templates/elements',
));
