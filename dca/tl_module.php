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
                                                                  {catalog_legend},catalog_categories;
                                                                  {config_legend},catalog_featured,catalog_detailModule;
                                                                  {template_legend},numberOfItems,perPage,skipFirst,catalog_template;
                                                                  {product_legend},productClass,imgSize;
                                                                  {meta_legend},catalog_price,catalog_date;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_detail']  = '{title_legend},name,headline,type;
                                                                  {catalog_legend},catalog_categories;
                                                                  {template_legend},catalog_template,imgSize;
                                                                  {type_legend},typeClass,typeImageSize;
                                                                  {meta_legend},catalog_price,catalog_date;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_related'] = '{title_legend},name,headline,type;
                                                                  {catalog_legend},catalog_categories;
                                                                  {config_legend},numberOfItems;
                                                                  {redirect_legend},jumpTo;
                                                                  {template_legend:hide},catalog_template,imgSize;
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
	'options_callback'     => array('tl_module_catalog', 'getCatalogs'),
	'eval'                 => array('multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['catalog_template'],
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_module_catalog', 'getCatalogTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_featured'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['catalog_featured'],
	'default'                 => 'all',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('all', 'featured', 'unfeatured'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(20) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_price'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['catalog_price'],
	'exclude'                 => true,
	'flag'                    => 1,
	'inputType'               => 'checkbox',
	'eval'                    => array('doNotCopy'=>true, 'tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog_date'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['catalog_date'],
	'exclude'                 => true,
	'flag'                    => 1,
	'inputType'               => 'checkbox',
	'eval'                    => array('doNotCopy'=>true, 'tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
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
$GLOBALS['TL_DCA']['tl_module']['fields']['productClass'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['productClass'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['typeClass'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['typeClass'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['typeImageSize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['typeImageSize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'options'                 => $GLOBALS['TL_CROP'],
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'digit', 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);


/**
 * Class tl_module_cds
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Hamid Abbaszadeh 2010
 * @author     Hamid Abbaszadeh <http://respinar.com>
 * @package    Carpets Collection
 */
class tl_module_catalog extends Backend
{

	/**
	 * Get all news archives and return them as array
	 * @return array
	 */
	public function getCatalogs()
	{
		//if (!$this->User->isAdmin && !is_array($this->User->news))
		//{
		//	return array();
		//}

		$arrArchives = array();
		$objArchives = $this->Database->execute("SELECT id, title FROM tl_catalog ORDER BY title");

		while ($objArchives->next())
		{
			//if ($this->User->hasAccess($objArchives->id, 'news'))
			//{
				$arrArchives[$objArchives->id] = $objArchives->title;
			//}
		}

		return $arrArchives;
	}

	/**
	 * Return all prices templates as array
	 * @param object
	 * @return array
	 */
	public function getCatalogTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('catalog_', $dc->activeRecord->pid);
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
