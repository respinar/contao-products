<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Products
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Respinar\Products\Frontend\Module;

use Respinar\Products\Model\ProductModel;
use Respinar\Products\Model\ProductCatalogModel;

/**
 * Class ModuleProduct
 *
 * Parent class for product modules.
 * @copyright  Hamid Abbaszadeh 2014
 * @author     Hamid Abbaszadeh <https://respinar.com>
 * @package    product
 */
abstract class ModuleProduct extends \Module
{

	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();


	/**
	 * Sort out protected catalogs
	 * @param array
	 * @return array
	 */
	protected function sortOutProtected($arrCatalogs)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrCatalogs) || empty($arrCatalogs))
		{
			return $arrCatalogs;
		}

		$this->import('FrontendUser', 'User');
		$objCatalog = ProductCatalogModel::findMultipleByIds($arrCatalogs);
		$arrCatalogs = array();

		if ($objCatalog !== null)
		{
			while ($objCatalog->next())
			{
				if ($objCatalog->protected)
				{
					if (!FE_USER_LOGGED_IN)
					{
						continue;
					}

					$groups = deserialize($objCatalog->groups);

					if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
					{
						continue;
					}
				}

				$arrCatalogs[] = $objCatalog->id;
			}
		}

		return $arrCatalogs;
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	public function parseProduct($objProduct, $blnAddCategory=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($this->product_template);
		$objTemplate->setData($objProduct->row());	

		$objTemplate->class = (($this->product_Class != '') ? ' ' . $this->product_Class : '') . $strClass;

		if (time() - $objProduct->date < 2592000) {
			$objTemplate->new_product = true;
		}		

		$objTemplate->category    = $objProduct->getRelated('pid');

		$objTemplate->count = $intCount; // see #5708

		$arrMeta = $this->getMetaFields($objProduct);

		// Add the meta information
		$objTemplate->date         = $arrMeta['date'];
		$objTemplate->meta_brand   = $arrMeta['brand'];
		$objTemplate->meta_price   = $arrMeta['price'];
		$objTemplate->meta_availability = $arrMeta['availability'];
		$objTemplate->meta_availability_txt = $GLOBALS['TL_LANG']['MSC'][$objProduct->availability];
		$objTemplate->meta_model   = $arrMeta['model'];
		$objTemplate->meta_global_ID = $arrMeta['global_ID'];
		$objTemplate->meta_sku     = $arrMeta['sku'];
		$objTemplate->meta_buy     = $arrMeta['buy'];

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

		$objElement = \ContentModel::findPublishedByPidAndTable($objProduct->id, 'tl_product');

		if ($objElement !== null)
		{
			while ($objElement->next())
			{
				$objTemplate->text .= $this->getContentElement($objElement->current());
			}

			$objTemplate->link        = $this->generateProductUrl($objProduct, $blnAddCategory);
		}		

		$objTemplate->addImage = false;

		// Add an image
		if ($objProduct->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objProduct->singleSRC);

			if ($objModel !== null && is_file(\System::getContainer()->getParameter('kernel.project_dir') . '/' . $objModel->path))			
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrProduct = $objProduct->row();

				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = \StringUtil::deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrProduct['size'] = $this->imgSize;
					}
				}

				$arrProduct['singleSRC'] = $objModel->path;

				$this->addImageToTemplate($objTemplate, $arrProduct, null, null, $objModel);

				// Link to the product detail if no image link has been defined		
				$picture = $objTemplate->picture;
				unset($picture['title']);
				$objTemplate->picture = $picture;

				$objTemplate->href = $objTemplate->link;
				$objTemplate->linkTitle = \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['moreDetail'], $objProduct->title), true);
				
			}
		}

		$objTemplate->enclosure = array();

		// Add enclosures
		if ($objProduct->addEnclosure)
		{
			$this->addEnclosuresToTemplate($objTemplate, $objProduct->row());
		}		
		
		$objTemplate->featured_text = "Featured";
		$objTemplate->new_text      = "New";


		return $objTemplate->parse();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseProducts($objProducts, $blnAddCategory=false)
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

			$arrProducts[] = $this->parseProduct($objProduct, $blnAddCategory, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : ''), $count);
		}

		return $arrProducts;
	}

	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateProductUrl($objItem, $blnAddCategory=false)
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
			$objPage = \PageModel::findByPk($objItem->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				self::$arrUrlCache[$strCacheKey] = ampersand(\Environment::get('request'), true);
			}
			else
			{
				self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objPage->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $objItem->alias != '') ? $objItem->alias : $objItem->id)));
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
	protected function generateLink($strLink, $objProduct, $blnAddCategory=false, $blnIsReadMore=false)
	{

		return sprintf('<a href="%s" title="%s">%s%s</a>',
						$this->generateProductUrl($objProduct, $blnAddCategory),
						specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objProduct->title), true),
						$strLink,
						($blnIsReadMore ? ' <span class="invisible">'.$objProduct->title.'</span>' : ''));

	}

	/**
	 * Return the meta fields of a product as array
	 * @param object
	 * @return array
	 */
	protected function getMetaFields($objProduct)
	{
		$meta = deserialize($this->product_metaFields);

		if (!is_array($meta))
		{
			return array();
		}

		global $objPage;
		$return = array();

		foreach ($meta as $field)
		{
			switch ($field)
			{
				case 'date':
					$return['date'] = \Date::parse($objPage->datimFormat, $objProduct->date);
					break;

				case 'price':
					if ($objProduct->price) 
						$return['price'] = \StringUtil::deserialize($objProduct->price);						
						$return['price']['symbol'] = $GLOBALS['TL_LANG']['MSC'][$return['price']['unit']];
					break;
				
				case 'availability':
					if ($objProduct->availability) 
						$return['availability'] = $objProduct->availability;
					break;

				case 'global_ID':
					if ($objProduct->global_ID) 
						$return['global_ID'] = \StringUtil::deserialize($objProduct->global_ID);
					break;

				case 'model':
					if ($objProduct->model) 
						$return['model'] = $objProduct->model;
					break;

				case 'brand':
					if ($objProduct->brand) 
						$return['brand'] = $objProduct->brand;
					break;
				
				case 'sku':
					if ($objProduct->sku) 
						$return['sku'] = $objProduct->sku;
					break;
				
				case 'buy':
					if ($objProduct->url) 
						$return['buy'] = $objProduct->url;
					break;
			}
		}

		return $return;
	}

		/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseRelated($objProduct, $blnAddCategory=false, $strClass='', $intCount=0)
	{
		$objTemplate = new \FrontendTemplate($this->related_template);
		$objTemplate->setData($objProduct->row());
		$objTemplate->class = (($this->related_Class != '') ? ' ' . $this->related_Class : '') . $strClass;
		if (time() - $objProduct->date < 2592000) {
			$objTemplate->new_product = true;
		}
		$objTemplate->link        = $this->generateProductUrl($objProduct, $blnAddCategory);
		$arrMeta = $this->getMetaFields($objProduct);
		$objTemplate->category    = $objProduct->getRelated('pid');
		$objTemplate->count = $intCount; // see #5708
		$arrMeta = $this->getMetaFields($objProduct);

		// Add the meta information
		$objTemplate->date = $arrMeta['date'];
		$objTemplate->meta_brand = $arrMeta['brand'];
		$objTemplate->meta_model = $arrMeta['model'];
		$objTemplate->meta_global_ID = $arrMeta['global_ID'];
		$objTemplate->meta_sku = $arrMeta['sku'];
		$objTemplate->meta_buy = $arrMeta['buy'];

		$objTemplate->meta_brand_txt = $GLOBALS['TL_LANG']['MSC']['brand_text'];
		$objTemplate->meta_model_txt = $GLOBALS['TL_LANG']['MSC']['model_text'];
		$objTemplate->meta_global_ID_txt  = $GLOBALS['TL_LANG']['MSC']['global_ID_text'];
		$objTemplate->meta_sku_txt   = $GLOBALS['TL_LANG']['MSC']['sku_text'];

		$objTemplate->hasMetaFields = !empty($arrMeta);
		$objTemplate->timestamp = $objProduct->date;
		$objTemplate->datetime = date('Y-m-d\TH:i:sP', $objProduct->date);

		$objTemplate->addImage = false;
		// Add an image
		if ($objProduct->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objProduct->singleSRC);
			if ($objModel !== null && is_file(\System::getContainer()->getParameter('kernel.project_dir') . '/' . $objModel->path))			
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrProduct = $objProduct->row();
				// Override the default image size
				if ($this->related_imgSize != '')
				{
					$size = \StringUtil::deserialize($this->related_imgSize);
					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrProduct['size'] = $this->related_imgSize;
					}
				}

				$arrProduct['singleSRC'] = $objModel->path;

				$this->addImageToTemplate($objTemplate, $arrProduct, null, null, $objModel);

				// Link to the products
				// Unset the image title attribute
				$picture = $objTemplate->picture;
				unset($picture['title']);
				$objTemplate->picture = $picture;

				$objTemplate->href = $objTemplate->link;
				$objTemplate->linkTitle = \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['moreDetail'], $objProduct->title), true);
			
			}
		}
		return $objTemplate->parse();
	}
	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseRelateds($objProducts, $blnAddCategory=false)
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

			$arrProducts[] = $this->parseRelated($objProduct, $blnAddCategory, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : ''), $count);
		}

		return $arrProducts;
	}

}
