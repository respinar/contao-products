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

use Contao\System;
use Respinar\ProductsBundle\EventListener\DataContainer\ModuleDcaListener;


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['product_list']    = '{title_legend},name,headline,type;{catalog_legend},product_catalogs,product_categories,product_featured,product_detailModule,product_sortBy,numberOfItems,perPage,skipFirst;{template_legend},product_metaFields,customTpl;{product_legend},product_template,imgSize,product_list_Class,product_Class;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['product_detail']  = '{title_legend},name,headline,type;{catalog_legend},product_catalogs;{template_legend},product_metaFields,customTpl;{product_legend},product_template,imgSize;{related_legend},related_show,related_template,related_imgSize,product_list_Class,related_Class;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['product_catalogs'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['product_catalogs'],
	'exclude'              => true,
	'inputType'            => 'checkbox',
	'foreignKey'           => 'tl_product_catalog.title',
	'eval'                 => array('multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_categories'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_categories'],
    'exclude'                 => true,
    'inputType'               => 'treePicker',
    'foreignKey'              => 'tl_product_category.title',
    'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'foreignTable'=>'tl_product_category', 'titleField'=>'title', 'searchField'=>'title', 'managerHref'=>'table=tl_product_category'),
    'sql'                     => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_featured'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_featured'],
	'default'                 => 'all_product',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('all_product', 'featured_product', 'unfeatured_product'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(20) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_sortBy'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_sortBy'],
	'default'                 => 'custom',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('custom','date_desc', 'date_asc','title_asc', 'title_desc'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(16) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_metaFields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_metaFields'],
	'default'                 => array(''),
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('date','price','availability','brand','model','sku','global_ID','buy'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_detailModule'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_detailModule'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array(ModuleDcaListener::class, 'getDetailModules'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['product_template'],
	'default'              => 'product_short',
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array(ModuleDcaListener::class, 'getProductTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_list_Class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_list_Class'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_Class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_Class'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['related_show'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['related_show'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array(),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['related_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['related_template'],
	'default'              => 'product_related',
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array(ModuleDcaListener::class, 'getRelatedTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['related_Class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['related_Class'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['related_imgSize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['related_imgSize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'options'                 => System::getImageSizes(),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'digit', 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);

$bundles = System::getContainer()->getParameter('kernel.bundles');

// Add the comments template drop-down menu
if (isset($bundles['ContaoCommentsBundle']))
{
	$GLOBALS['TL_DCA']['tl_module']['palettes']['product_detail'] = str_replace('{protected_legend:hide}', '{comment_legend:hide},com_template;{protected_legend:hide}', $GLOBALS['TL_DCA']['tl_module']['palettes']['product_detail']);
}