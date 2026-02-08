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
	$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields'] = ['title', 'brand','model','sku', 'published'];

}


$GLOBALS['TL_DCA']['tl_content']['palettes']['product_single']  = '
	{type_legend},type,headline;
  {product_legend},product;
	{template_legend},customTpl,product_template;
	{image_legend},size;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_content']['palettes']['product_list'] = '
	{type_legend},type,headline;
	{product_legend},products;
	{template_legend},customTpl,product_listClass,product_template;
	{image_legend},size;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_content']['palettes']['product_catalog'] = '
	{type_legend},type,headline;
	{product_legend},product_catalogs;
	{template_legend},customTpl,product_listClass,product_template;
	{image_legend},size;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['product'] = [
	'exclude'    => true,
	'inputType'  => 'select',
	'foreignKey' => 'tl_product.title',
	'eval'       => ['helpwizard'=>true, 'chosen'=>true, 'mandatory'=>true],
   'sql'       => "varchar(64) NOT NULL default ''"
	];
$GLOBALS['TL_DCA']['tl_content']['fields']['products'] = [
	'exclude'    => true,
	'inputType'  => 'select',
	'foreignKey' => 'tl_product.title',
	'eval'       => ['helpwizard'=>true, 'chosen'=>true, 'multiple'=>true, 'mandatory'=>true],
  'sql'        => "blob NULL"
];
$GLOBALS['TL_DCA']['tl_content']['fields']['product_catalogs'] = [
	'exclude'    => true,
	'inputType'  => 'checkbox',
	'foreignKey' => 'tl_product_catalog.title',
	'eval'       => ['helpwizard'=>true, 'chosen'=>true, 'multiple'=>true, 'mandatory'=>true],
  'sql'        => "blob NULL"
];
$GLOBALS['TL_DCA']['tl_content']['fields']['product_template'] = [
	'default'    => 'product_short',
	'exclude'    => true,
	'inputType'  => 'select',
	'eval'       => ['tl_class'=>'w50 clr'],
  'sql'        => "varchar(64) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_content']['fields']['product_listClass'] = [
	'exclude'    => true,
	'inputType'  => 'text',
	'eval'       => ['maxlength'=>128, 'tl_class'=>'w50'],
	'sql'        => "varchar(255) NOT NULL default ''"
];
