<?php

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2023 <hamid@respinar.com>
 *
 * @license MIT
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Respinar\ProductsBundle\Controller;

use Contao\FrontendTemplate;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\System;
use Contao\Date;
use Contao\PageModel;
use Contao\Environment;
use Contao\StringUtil;
use Contao\Controller;
use Contao\Config;

use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\CatalogModel;

/**
 * Class ModuleProduct
 *
 * Parent class for product modules.
 * @copyright  Hamid Abbaszadeh 2014
 * @author     Hamid Abbaszadeh <https://respinar.com>
 * @package    product
 */
abstract class Product
{

	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();

	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	static public function parseProduct($objProduct, $model, $blnAddCategory=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new FrontendTemplate($model->product_template);
		$objTemplate->setData($objProduct->row());

		$objTemplate->class = (($model->product_Class != '') ? ' ' . $model->product_Class : '') . $strClass;

		if (time() - $objProduct->date < 2592000) {
			$objTemplate->new_product = true;
		}

		$objTemplate->category    = $objProduct->getRelated('pid');

		$objTemplate->count = $intCount; // see #5708

		$arrMeta = Product::getMetaFields($objProduct, $model);

		// print_r($model->product_metaFields);
		// print_r($arrMeta);
		// exit('STOP');

		// Add the meta information
		$objTemplate->meta_date    = $arrMeta['date'] ?? '';
		$objTemplate->meta_brand   = $arrMeta['brand'] ?? '';
		$objTemplate->meta_price   = $arrMeta['price'] ?? '';
		$objTemplate->meta_availability = $arrMeta['availability'] ?? '';
		$objTemplate->meta_availability_txt = $GLOBALS['TL_LANG']['MSC'][$objProduct->availability] ?? '';
		$objTemplate->meta_model   = $arrMeta['model'] ?? '';
		$objTemplate->meta_global_ID = $arrMeta['global_ID'] ?? '';
		$objTemplate->meta_sku     = $arrMeta['sku'] ?? '';
		$objTemplate->meta_buy     = $arrMeta['buy'] ?? '';

		$objTemplate->meta_price_txt   = $GLOBALS['TL_LANG']['MSC']['price_text'];
		$objTemplate->meta_brand_txt   = $GLOBALS['TL_LANG']['MSC']['brand_text'];
		$objTemplate->meta_model_txt   = $GLOBALS['TL_LANG']['MSC']['model_text'];
		$objTemplate->meta_global_ID_txt = $GLOBALS['TL_LANG']['MSC']['global_ID_text'];
		$objTemplate->meta_sku_txt     = $GLOBALS['TL_LANG']['MSC']['sku_text'];
		$objTemplate->meta_status_txt  = $GLOBALS['TL_LANG']['MSC']['status_text'];

		$objTemplate->meta_vote_txt    = $GLOBALS['TL_LANG']['MSC']['vote_text'];

		$objTemplate->hasMetaFields = !empty($arrMeta);
		$objTemplate->timestamp = $objProduct->date;
		$objTemplate->datetime = date('Y-m-d\TH:i:sP', $objProduct->date);

		$objElement = ContentModel::findPublishedByPidAndTable($objProduct->id, 'tl_product');

		if ($objElement !== null)
		{
			while ($objElement->next())
			{
				$objTemplate->text .= Controller::getContentElement($objElement->current());
			}

			$objTemplate->link = Product::generateProductUrl($objProduct, $blnAddCategory);
		}

		$objTemplate->addImage = false;

		// Add an image
		if ($objProduct->singleSRC)
		{
			$imgSize = null;

			if ($model->imgSize)
			{
				$size = StringUtil::deserialize($model->imgSize);

				if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_')
				{
					$imgSize = $model->imgSize;
				}
			}

			$figureBuilder = System::getContainer()
				->get('contao.image.studio')
				->createFigureBuilder()
				->from($objProduct->singleSRC)
				->setSize($imgSize);

			if (null !== ($figure = $figureBuilder->buildIfResourceExists()))
			{
				$figure->applyLegacyTemplateData($objTemplate);
			}


			// $objModel = FilesModel::findByUuid($objProduct->singleSRC);

			// if ($objModel !== null && is_file(System::getContainer()->getParameter('kernel.project_dir') . '/' . $objModel->path))
			// {
			// 	// Do not override the field now that we have a model registry (see #6303)
			// 	$arrProduct = $objProduct->row();

			// 	// Override the default image size
			// 	if ($model->imgSize != '')
			// 	{
			// 		$size = StringUtil::deserialize($model->imgSize);

			// 		if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
			// 		{
			// 			$arrProduct['size'] = $model->imgSize;
			// 		}
			// 	}

			// 	$arrProduct['singleSRC'] = $objModel->path;

			// 	// Link to the product detail if no image link has been defined
			// 	$picture = $objTemplate->picture;
			// 	unset($picture['title']);
			// 	$objTemplate->picture = $picture;

			// 	$objTemplate->href = $objTemplate->link;
			// 	$objTemplate->linkTitle = StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['moreDetail'], $objProduct->title), true);

			// 	$this->addImageToTemplate($objTemplate, $arrProduct, null, null, $objModel);

			// }

		}

		$objTemplate->enclosure = array();

		// Add enclosures
		if ($objProduct->addEnclosure)
		{
			Controller::addEnclosuresToTemplate($objTemplate, $objProduct->row());
		}

		$objTemplate->featured_text = "Featured";
		$objTemplate->new_text = "New";

		// schema.org information
		$objTemplate->getSchemaOrgData = static function () use ($objTemplate, $objProduct): array {
			$jsonLd = Product::getSchemaOrgData($objProduct);

			if ($objTemplate->figure)
			{
				$jsonLd['image'] = $objTemplate->figure->getSchemaOrgData();
			}

			return $jsonLd;
		};

		return $objTemplate->parse();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	static public function parseProducts($objProducts, $model, $blnAddCategory=false)
	{
		$limit = $objProducts->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrProducts = array();

		while ($objProducts->next())
		{
			$objProduct = $objProducts->current();

			$arrProducts[] = Product::parseProduct($objProduct, $model, $blnAddCategory, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : ''), $count);
		}

		return $arrProducts;
	}

	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseRelateds($objProducts, $model, $blnAddCategory=false)
	{

		$model->product_template = $model->related_template;
		$model->imgSize = $model->related_imgSize;
		$model->product_list_Class = $model->related_list_Class;
		$model->product_Class = $model->related_Class;

		$limit = $objProducts->count();
		if ($limit < 1)
		{
			return array();
		}
		$count = 0;
		$arrRelatedes = array();
		while ($objProducts->next())
		{
			$objProduct = $objProducts->current();

			$arrRelatedes[] = $this->parseProduct($objProduct, $model, $blnAddCategory, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : ''), $count);
		}

		return $arrRelatedes;
	}

	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	public static function generateProductUrl($objItem, $blnAddCategory=false)
	{
		$strCacheKey = 'id_' . $objItem->id;

		// Load the URL from cache
		if (isset(self::$arrUrlCache[$strCacheKey]))
		{
			return self::$arrUrlCache[$strCacheKey];
		}

		// Initialize the cache
		self::$arrUrlCache[$strCacheKey] = null;

		// Link to the default page
		if (self::$arrUrlCache[$strCacheKey] === null)
		{
			$objPage = PageModel::findByPk($objItem->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				self::$arrUrlCache[$strCacheKey] = StringUtil::ampersand(Environment::get('request'), true);
			}
			else
			{
				self::$arrUrlCache[$strCacheKey] = StringUtil::ampersand(Controller::generateFrontendUrl($objPage->row(), ((Config::get('useAutoItem') && !Config::get('disableAlias')) ?  '/' : '/items/') . ((!Config::get('disableAlias') && $objItem->alias != '') ? $objItem->alias : $objItem->id)));
			}

		}

		return self::$arrUrlCache[$strCacheKey];
	}


	/**
	 * Generate a link and return it as string
	 * @param string
	 * @param object
	 * @param boolean
	 * @param boolean
	 * @return string
	 */
	protected static function generateLink($strLink, $objProduct, $blnAddCategory=false, $blnIsReadMore=false)
	{

		return sprintf('<a href="%s" title="%s">%s%s</a>',
						Product::generateProductUrl($objProduct, $blnAddCategory),
						StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objProduct->title), true),
						$strLink,
						($blnIsReadMore ? ' <span class="invisible">'.$objProduct->title.'</span>' : ''));

	}

	/**
	 * Return the meta fields of a product as array
	 * @param object
	 * @return array
	 */
	protected static function getMetaFields($objProduct, $model)
	{
		$meta = StringUtil::deserialize($model->product_metaFields);

		if (!\is_array($meta))
		{
			return array();
		}

		global $objPage;
		$return = array();

		if (\in_array("date", $meta)){
			$return['date'] = Date::parse($objPage->datimFormat, $objProduct->date);
		}

		if (\in_array("price", $meta) && isset($objProduct->price)){
			$return['price'] = StringUtil::deserialize($objProduct->price);
			$return['price']['symbol'] = $GLOBALS['TL_LANG']['MSC'][$return['price']['unit']];
			$return['price']['priceValidUntil'] = date('Y-m-d\TH:i:sP', $objProduct->priceValidUntil);
			$return['price']['url'] = $objProduct->url ?? '';
		}

		if (\in_array("availability", $meta) && isset($objProduct->availability)){
			$return['availability'] = $objProduct->availability;
		}

		if (\in_array("global_ID", $meta) && isset($objProduct->global_ID)){
			$return['global_ID'] = StringUtil::deserialize($objProduct->global_ID);
		}

		if (\in_array("model", $meta) && isset($objProduct->model)){
			$return['model'] = $objProduct->model;
		}

		if (\in_array("brand", $meta) && isset($objProduct->brand)){
			$return['brand'] = $objProduct->brand;
		}

		if (\in_array("sku", $meta) && isset($objProduct->sku)){
			$return['sku'] = $objProduct->sku;
		}

		if (\in_array("buy", $meta) && isset($objProduct->url)){
			$return['buy'] = $objProduct->url;
		}

		return $return;
	}

	/**
	 * Return the schema.org data from a product
	 *
	 * @return array
	 */
	public static function getSchemaOrgData($objProduct): array
	{
		$htmlDecoder = System::getContainer()->get('contao.string.html_decoder');
		$price = StringUtil::deserialize($objProduct->price);
		$global_ID = StringUtil::deserialize($objProduct->global_ID);

		$jsonLd = array(
			"@type" => "Product",
			"name" => $htmlDecoder->inputEncodedToPlainText($objProduct->title),
			"description" => $objProduct->description,
			"sku" => $objProduct->sku,
			$global_ID['unit'] => $global_ID['value'],
			"brand" => array(
				"@type" => "Brand",
				"name" => $objProduct->brand
			),
		);

		if (true)
		{
			$jsonLd['aggregateRating'] = array(
				'@type' => 'AggregateRating',
				"ratingValue" => $objProduct->rating_value,
   				"reviewCount" => $objProduct->rating_count
			);
		}

		if (true)
		{
			$jsonLd['offers'] = array(
				'@type' => 'Offer',
				"priceCurrency" => $price['unit'],
				"price" => $price['value'],
				"priceValidUntil" => date('Y-m-d\TH:i:sP', $objProduct->priceValidUntil),
				"availability"=> "http://schema.org/" . $objProduct->availability,
				"seller" => array(
					"@type" => "Organization",
					"name" => $objProduct->brand
				),
				"ratingValue" => $objProduct->rating_value,
   				"reviewCount" => $objProduct->rating_count
			);
		}

		return $jsonLd;
	}

}
