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
 * Class ModuleCatalogList
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModuleCatalogList extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_catalog_list';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['catalog_reader'][0]) . ' ###';
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

		$objCatalogCategory = $this->Database->prepare("SELECT * FROM tl_catalog_category WHERE alias=?")->execute(\Input::get('items'));

		$this->Template->categorytitle = $objCatalogCategory->title;

		$objCatalogProduct = $this->Database->prepare("SELECT * FROM tl_catalog_product WHERE published=1 AND pid=? ORDER BY sorting")->execute($objCatalogCategory->id);

		// Return if no products were found
		if (!$objCatalogProduct->numRows)
		{
			$this->Template = new \FrontendTemplate('mod_catalog_empty');
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyCatalog'];
			return;
		}

		$strLink = '';

		// Generate a jumpTo link
		if ($this->jumpTo > 0)
		{
			$objJump = \PageModel::findByPk($this->jumpTo);

			if ($objJump !== null)
			{
				$strLink = $this->generateFrontendUrl($objJump->row(), ($GLOBALS['TL_CONFIG']['useAutoItem'] ?  '/%s' : '/items/%s'));
			}
		}

		$arrCatalogList = array();

		while ($objCatalogProduct->next())
		{

			$strImage = '';
			$objImage = \FilesModel::findByPk($objCatalogProduct->singleSRC);

			// Add photo image
			if ($objImage !== null)
			{
				$strImage = \Image::getHtml(\Image::get($objImage->path, '300', '200', 'center_center'));
			}

			$arrCatalogList[] = array
			(
				'title' => $objCatalogProduct->title,
				'code'  => $objCatalogProduct->code,
				'image' => $strImage,
				'link'  => strlen($strLink) ? sprintf($strLink, $objCatalogProduct->code) : ''
			);
		}

		$this->Template->cataloglist = $arrCatalogList;

	}
}
