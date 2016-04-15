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
namespace product;


/**
 * Class ModuleProductDetail
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModuleProductDetail extends \ModuleProduct
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_product_detail';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['product_detail'][0]) . ' ###';
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

		$this->catalogs = $this->sortOutProtected(deserialize($this->catalogs));

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		global $objPage;

		$this->Template->products = '';
		$this->Template->referer = 'javascript:history.go(-1)';
		$this->Template->back              = $GLOBALS['TL_LANG']['MSC']['goBack'];
		$this->Template->types_headline    = $GLOBALS['TL_LANG']['MSC']['types_headline'];
		$this->Template->relateds_headline = $GLOBALS['TL_LANG']['MSC']['relateds_headline'];

		$objProduct = \ProductModel::findPublishedByParentAndIdOrAlias(\Input::get('items'),$this->catalogs);

		// Overwrite the page title
		if ($objProduct->title != '')
		{
			$objPage->pageTitle = strip_tags(strip_insert_tags($objProduct->title));
		}

		// Overwrite the page description
		if ($objProduct->description != '')
		{
			$objPage->description = $this->prepareMetaDescription($objProduct->description);
		}

		$arrProduct = $this->parseProduct($objProduct);

		$this->Template->product = $arrProduct;


		$objProduct->related = deserialize($objProduct->related);

		if (!empty($objProduct->related)) {

			$objProducts = \ProductModel::findPublishedByIds($objProduct->related);

			$this->Template->relateds = $this->parseRelateds($objProducts);
		}




	}
}
