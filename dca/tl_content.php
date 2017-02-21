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
if (Input::get('do') == 'catalogs')
{
	$GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_product';
	$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields'] = array('title', 'brand','model','code','sku', 'published');

}


$GLOBALS['TL_DCA']['tl_content']['palettes']['product']  = '{type_legend},name,headline,type;
                                                            {product_legend},product;
                                                            {template_legend},product_metaFields,customTpl,size;
                                                            {protected_legend:hide},protected;
                                                            {expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['product'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_content']['product'],
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_content_product', 'getProducts'),
	'eval'                 => array('helpwizard'=>true, 'chosen'=>true, 'mandatory'=>true),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['product_metaFields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_metaFields'],
	'default'                 => array(''),
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('date','code','brand','model','sku','buy'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
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
	 * Get all news archives and return them as array
	 * @return array
	 */
	public function getProducts()
	{
		//if (!$this->User->isAdmin && !is_array($this->User->news))
		//{
		//	return array();
		//}

		$arrProducts = array();
		$objProducts = $this->Database->execute("SELECT id, title FROM tl_product WHERE published=1 ORDER BY title");

		while ($objProducts->next())
		{
			//if ($this->User->hasAccess($objArchives->id, 'news'))
			//{
				$arrProducts[$objProducts->id] = $objProducts->title;
			//}
		}

		return $arrProducts;
	}

	/**
	 * Return all prices templates as array
	 * @param object
	 * @return array
	 */
	public function getProductTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('product_', $dc->activeRecord->pid);
	}
    
}
