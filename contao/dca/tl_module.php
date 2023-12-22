<?php

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2023 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\System;
use Contao\BackendUser;

/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['product_list']    = '
	{title_legend},name,headline,type;
	{catalog_legend},product_catalogs,product_featured,product_detailModule,product_sortBy,numberOfItems,perPage,skipFirst;
	{template_legend},product_metaFields,customTpl;
	{product_legend},product_template,imgSize,product_listClass,product_singleClass;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['product_detail']  = '
	{title_legend},name,headline,type;
	{catalog_legend},product_catalogs,overviewPage,customLabel;
	{meta_legend},product_metaFields;
	{template_legend},customTpl,product_template,product_summary,imgSize;
	{related_legend},product_related,product_relatedTpl,product_relatedImgSize,product_listClass,product_singleClass;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['product_catalogs'] = array
(
	'exclude'              => true,
	'inputType'            => 'checkbox',
	'foreignKey'           => 'tl_product_catalog.title',
	'eval'                 => array('multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_categories'] = array
(
    'exclude'                 => true,
    'inputType'               => 'treePicker',
    'foreignKey'              => 'tl_product_category.title',
    'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'foreignTable'=>'tl_product_category', 'titleField'=>'title', 'searchField'=>'title', 'managerHref'=>'table=tl_product_category'),
    'sql'                     => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_featured'] = array
(
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
	'exclude'                 => true,
	'inputType'               => 'select',
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_template'] = array
(
	'default'              => 'product_short',
	'exclude'              => true,
	'inputType'            => 'select',
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_listClass'] = array
(
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_singleClass'] = array
(
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_related'] = array
(
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array(),
	'sql'                     => array('type' => 'boolean', 'default' => false)
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_summary'] = array
(
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => array('type' => 'boolean', 'default' => false)
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_relatedTpl'] = array
(
	'default'              => 'product_related',
	'exclude'              => true,
	'inputType'            => 'select',
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_relatedImgSize'] = array
(
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'options_callback' => function () {
		return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
	},
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(128) COLLATE ascii_bin NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_comHeadline'] = array
(
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'inputUnit',
	'options'                 => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
	'eval'                    => array('maxlength'=>200, 'tl_class'=>'w50 clr'),
	'sql'                     => "varchar(255) NOT NULL default 'a:2:{s:5:\"value\";s:0:\"\";s:4:\"unit\";s:2:\"h2\";}'"
);

$bundles = System::getContainer()->getParameter('kernel.bundles');

// Add the comments template drop-down menu
if (isset($bundles['ContaoCommentsBundle']))
{
	$GLOBALS['TL_DCA']['tl_module']['palettes']['product_detail'] = str_replace('{protected_legend:hide}', '{comment_legend:hide},product_comHeadline,com_template;{protected_legend:hide}', $GLOBALS['TL_DCA']['tl_module']['palettes']['product_detail']);
}