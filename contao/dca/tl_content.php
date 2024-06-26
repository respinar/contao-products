<?php

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\Input;

 /**
 * Dynamically add the permission check and parent table
 */
if (Input::get('do') == 'products')
{
	$GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_product';
	$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields'] = array('title', 'brand','model','sku', 'published');

}


$GLOBALS['TL_DCA']['tl_content']['palettes']['product_single']  = '
	{type_legend},type,headline;
    {product_legend},product;
	{template_legend},product_template,customTpl,product_metaFields,size;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_content']['palettes']['product_list'] = '
	{type_legend},type,headline;
	{product_legend},products;
	{template_legend},product_template,customTpl,product_metaFields,size,product_listClass;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_content']['palettes']['product_catalog'] = '
	{type_legend},type,headline;
	{product_legend},product_catalogs;
	{template_legend},product_template,customTpl,product_metaFields,size,product_listClass;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['product'] = array
(
	'exclude'              => true,
	'inputType'            => 'select',
	'foreignKey'           => 'tl_product.title',
	'eval'                 => array('helpwizard'=>true, 'chosen'=>true, 'mandatory'=>true),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['products'] = array
(
	'exclude'              => true,
	'inputType'            => 'select',
	'foreignKey'           => 'tl_product.title',
	'eval'                 => array('helpwizard'=>true, 'chosen'=>true, 'multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['product_catalogs'] = array
(
	'exclude'              => true,
	'inputType'            => 'checkbox',
	'foreignKey'           => 'tl_product_catalog.title',
	'eval'                 => array('helpwizard'=>true, 'chosen'=>true, 'multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['product_metaFields'] = array
(
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('date','global_ID','brand','model','sku','buy'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true,'tl_class'=>'clr'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['product_template'] = array
(
	'default'              => 'product_short',
	'exclude'              => true,
	'inputType'            => 'select',
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['product_listClass'] = array
(
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
