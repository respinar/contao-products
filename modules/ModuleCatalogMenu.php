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
 * Class ModuleCatalogMenu
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModuleCatalogMenu extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_catalog_menu';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['catalog_menu'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		$objCatalog = $this->Database->prepare("SELECT * FROM tl_catalog WHERE id=?")->execute($this->catalog);

		$this->Template->catalogtitle = $objCatalog->title;

		$objCatalogCategory = $this->Database->prepare("SELECT * FROM tl_catalog_category WHERE published=1 AND pid=? ORDER BY sorting")->execute($this->catalog);

		// Return if no Catalog were found
		if (!$objCatalogCategory->numRows)
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

		$arrCatalogMenu = array();

		// Generate Catalog Category
		while ($objCatalogCategory->next())
		{

			$strImage = '';
			$objImage = \FilesModel::findByPk($objCatalogCategory->singleSRC);

			// Add photo image
			if ($objImage !== null)
			{
				$strImage = \Image::getHtml(\Image::get($objImage->path, '100', '140', 'center_center'));
			}

			$arrCatalogMenu[] = array
			(
				'title' => $objCatalogCategory->title,
				'image' => $strImage,
				'link'  => strlen($strLink) ? sprintf($strLink, $objCatalogCategory->alias) : ''
			);
		}

		$this->Template->catalogmenu = $arrCatalogMenu;

	}
}
