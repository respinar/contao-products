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
 * Namespace
 */
namespace catalog;


/**
 * Class ModuleCatalogRelated
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModuleCatalogRelated extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_catalog_related';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['carpet_related'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
        {
			\Input::setGet('items', \Input::get('auto_item'));
        }

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

	}
}
