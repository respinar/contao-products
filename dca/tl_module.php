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

$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_menu']    = '{title_legend},name,headline,type;
                                                                  {catalog_legend},catalog;
                                                                  {redirect_legend},jumpTo;
                                                                  {template_legend:hide},catalog_template;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_list']    = '{title_legend},name,headline,type;
                                                                  {config_legend},numberOfItems,perPage;
                                                                  {redirect_legend},jumpTo;
                                                                  {template_legend:hide},catalog_template,imgSize;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_product'] = '{title_legend},name,headline,type;
                                                                  {template_legend:hide},catalog_template,imgSize;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['catalog_related'] = '{title_legend},name,headline,type;
                                                                  {config_legend},numberOfItems;
                                                                  {redirect_legend},jumpTo;
                                                                  {template_legend:hide},catalog_template,imgSize;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['catalog'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['catalog'],
	'exclude'              => true,
	'inputType'            => 'radio',
	'foreignKey'           => 'tl_catalog.title',
	'eval'                 => array('multiple'=>false, 'mandatory'=>true),
    'sql'                  => "int(10) unsigned NOT NULL"
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
	 * Return all prices templates as array
	 * @param object
	 * @return array
	 */
	public function getCatalogTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('catalog_', $dc->activeRecord->pid);
	}
}
