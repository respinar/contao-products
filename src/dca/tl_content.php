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

 /**
 * Dynamically add the permission check and parent table
 */
if (Input::get('do') == 'products')
{
	$GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_product';
	$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields'] = array('title', 'brand','model','sku', 'published');

}


$GLOBALS['TL_DCA']['tl_content']['palettes']['product']  = '{type_legend},type,headline;
                                                            {product_legend},product;
                                                            {template_legend},product_template,customTpl,product_metaFields,size;
                                                            {protected_legend:hide},protected;
															{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_content']['palettes']['products'] = '{type_legend},type,headline;
                                                            {product_legend},products;
                                                            {template_legend},product_template,customTpl,product_metaFields,size,product_list_Class;
                                                            {protected_legend:hide},protected;
                                                            {expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['product'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['product'],
	'exclude'              => true,
	'inputType'            => 'select',
	'foreignKey'           => 'tl_product.title',
	'eval'                 => array('helpwizard'=>true, 'chosen'=>true, 'mandatory'=>true),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['products'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['products'],
	'exclude'              => true,
	'inputType'            => 'select',
	'foreignKey'           => 'tl_product.title',
	'eval'                 => array('helpwizard'=>true, 'chosen'=>true, 'multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['product_metaFields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_metaFields'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('date','global_ID','brand','model','sku','buy'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true,'tl_class'=>'clr'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['product_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['product_template'],
	'default'              => 'product_short',
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_content_product', 'getProductTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['product_list_Class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['product_list_Class'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

/**
 * Class tl_content_product
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Hamid Abbaszadeh 2014
 * @author     Hamid Abbaszadeh <http://respinar.com>
 * @package    Catalog
 */
class tl_content_product extends Backend
{

	/**
	 * Return all prices templates as array
	 *
	 * @return array
	 */
	public function getProductTemplates()
	{
		return $this->getTemplateGroup('product_');
	}

}
