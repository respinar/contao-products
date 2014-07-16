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
 * Class ModuleCatalogProduct
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModuleCatalogProduct extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_catalog_product';

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

		$objCatalogProduct = $this->Database->prepare("SELECT * FROM tl_catalog_product WHERE code=?")->execute(\Input::get('items'));

		$objCatalogType = $this->Database->prepare("SELECT * FROM tl_catalog_type WHERE published=1 AND pid=?")->execute($objCatalogProduct->id);

		$this->Template->title = $objCatalogProduct->title;

		$this->Template->code  = $objCatalogProduct->code;
		$this->Template->price = $objCatalogProduct->price;
		$this->Template->spec  = deserialize($objCatalogProduct->spec);
		$this->Template->description  = $objCatalogProduct->description;

		$strImage = '';
		$objImage = \FilesModel::findByPk($objCatalogProduct->singleSRC);

		// Add photo image
		if ($objImage !== null)
		{
			$strImage = \Image::getHtml(\Image::get($objImage->path, '300', '200', 'center_center'));
		}

		$this->Template->image = $strImage;


		$arrProductType = array();

		while($objCatalogType->next())
		{
			$strImage = '';
			$objImage = \FilesModel::findByPk($objCatalogType->singleSRC);

			// Add photo image
			if ($objImage !== null)
			{
				$strImage = \Image::getHtml(\Image::get($objImage->path, '300', '200', 'center_center'));
			}

			$arrProductType[] = array
			(
				'title' => $objCatalogType->title,
				'code'  => $objCatalogType->code,
				'image' => $strImage,
			);
		}

		$this->Template->types = $arrProductType;

	}
}
