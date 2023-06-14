<?php

declare(strict_types=1);

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
namespace Respinar\ProductsBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Respinar\ProductsBundle\Controller\Product;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\ProductCatalogModel;


#[AsContentElement(category: "products")]
class ProductSingleController extends AbstractContentElementController
{

	public const TYPE = 'product_single';

	protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
		//
		return $template->getResponse();
	}

	/**
	 * Template
	 * @var string
	 */
	// protected $strTemplate = 'ce_product';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	// public function generate()
	// {
	// 	if (TL_MODE == 'BE')
	// 	{
	// 		$objTemplate = new \BackendTemplate('be_wildcard');

	// 		$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['product'][0]) . ' ###';

	// 		$objProduct = ProductModel::findBy('id',$this->product);

	// 		$objTemplate->title = $this->headline;
	// 		$objTemplate->id = $objProduct->id;
	// 		$objTemplate->link = $objProduct->title;
	// 		$objTemplate->href = 'contao/main.php?do=catalogs&amp;table=tl_product&amp;act=edit&amp;id=' . $objProduct->id;

	// 		$objFile = \FilesModel::findByUuid($objProduct->singleSRC);

	// 		$objTemplate->singleSRC = $objFile->path;

	// 		return $objTemplate->parse();
	// 	}

	// 	return parent::generate();
	// }


	/**
	 * Generate the module
	 */
	// protected function compile()
	// {

	// 	$objProduct = ProductModel::findBy('id',$this->product);

	// 	if (null === $objProduct)
	// 	{
	// 		echo "not found";
	// 	}

	// 	$arrProduct = $this->parseProduct($objProduct);

	// 	$this->Template->product = $arrProduct;

	// }

}
