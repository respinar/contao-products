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
 * Namespace
 */
namespace Respinar\Products;


/**
 * Class ContentProduct
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ContentProductSingle extends \ContentProduct
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_product';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['product'][0]) . ' ###';

			$objProduct = \ProductModel::findPublishedByIdOrAlias($this->product);

			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $objProduct->title;
			$objTemplate->href = 'contao/main.php?do=catalogs&amp;table=tl_product&amp;act=edit&amp;id=' . $this->id;

			$objFile = \FilesModel::findByUuid($objProduct->singleSRC);

			$objTemplate->singleSRC = $objFile->path;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		$objProduct = \ProductModel::findPublishedByIdOrAlias($this->product);

		if (null === $objProduct)
		{
			echo "not found";
		}

		$arrProduct = $this->parseProduct($objProduct);

		$this->Template->product = $arrProduct;

	}

}