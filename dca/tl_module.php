<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   catalog
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL
 * @copyright 2014
 */

/**
 * Add palettes to tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_list']    = '{title_legend},name,headline,type;
                                                                  {catalog_legend},catalog_categories,catalog_featured,catalog_detailModule,catalog_sortBy,numberOfItems,perPage,skipFirst;
                                                                  {template_legend},catalog_metaFields,product_template,customTpl;
                                                                  {product_legend},product_Class,perRow,imgSize;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_detail']  = '{title_legend},name,headline,type;
                                                                  {catalog_legend},catalog_categories;
                                                                  {template_legend},catalog_metaFields,product_template,customTpl;
                                                                  {image_legend},imgSize,fullsize;
                                                                  {type_legend},type_Class,type_ImageSize;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_related'] = '{title_legend},name,headline,type;
                                                                  {catalog_legend},catalog_categories;
                                                                  {config_legend},numberOfItems;
                                                                  {redirect_legend},jumpTo;
                                                                  {template_legend:hide},catalog_metaFields,product_template,customTpl;
                                                                  {image_legend},imgSize;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_categories'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['catalog_categories'],
	'exclude'              => true,
	'inputType'            => 'checkbox',
	'options_callback'     => array('tl_module_catalog', 'getCategories'),
	'eval'                 => array('multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_featured'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['catalog_featured'],
	'default'                 => 'all_product',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('all_product', 'featured_product', 'unfeatured_product'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(20) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_sortBy'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['catalog_sortBy'],
	'default'                 => 'custom',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('custom','title_asc', 'title_desc', 'date_asc', 'date_desc'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(16) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_metaFields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['catalog_metaFields'],
	'default'                 => array('date'),
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('date','price','rating'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_detailModule'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['catalog_detailModule'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_catalog', 'getDetailModules'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['product_template'],
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_module_catalog', 'getProductTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['perRow'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['perRow'],
	'default'              => '4',
	'exclude'              => true,
	'inputType'            => 'select',
	'options'              => array('1','2','3','4','6','12'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['product_Class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['product_Class'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['type_Class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['type_Class'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['type_ImageSize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['type_ImageSize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'options'                 => System::getImageSizes(),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'digit', 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);


/**
 * Class tl_module_catalog
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Hamid Abbaszadeh 2014
 * @author     Hamid Abbaszadeh <http://respinar.com>
 * @package    Catalog
 */
class tl_module_catalog extends Backend
{

	/**
	 * Get all news archives and return them as array
	 * @return array
	 */
	public function getCategories()
	{
		//if (!$this->User->isAdmin && !is_array($this->User->news))
		//{
		//	return array();
		//}

		$arrCategories = array();
		$objCategories = $this->Database->execute("SELECT id, title FROM tl_catalog_category ORDER BY title");

		while ($objCategories->next())
		{
			//if ($this->User->hasAccess($objArchives->id, 'news'))
			//{
				$arrCategories[$objCategories->id] = $objCategories->title;
			//}
		}

		return $arrCategories;
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

	/**
	 * Get all product detail modules and return them as array
	 * @return array
	 */
	public function getDetailModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='catalog_detail' ORDER BY t.name, m.name");

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}
}
