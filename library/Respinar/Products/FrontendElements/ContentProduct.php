<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package News
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Respinar\Products;


/**
 * Class ContentProduct
 *
 * Parent class for product modules.
 * @copyright  Hamid Abbaszadeh 2014
 * @author     Hamid Abbaszadeh <https://respinar.com>
 * @package    product
 */
abstract class ContentProduct extends \ContentElement
{

	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();


	/**
	 * Sort out protected archives
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
		$objCatalog = \ProductCatalogModel::findMultipleByIds($arrCatalogs);
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
		$objTemplate->date = $arrMeta['date'];
		$objTemplate->meta_brand = $arrMeta['brand'];
		$objTemplate->meta_model = $arrMeta['model'];
		$objTemplate->meta_code = $arrMeta['code'];
		$objTemplate->meta_sku = $arrMeta['sku'];
		$objTemplate->meta_buy = $arrMeta['buy'];

		$objTemplate->hasMetaFields = !empty($arrMeta);
		$objTemplate->timestamp = $objProduct->date;
		$objTemplate->datetime = date('Y-m-d\TH:i:sP', $objProduct->date);
		

		$objTemplate->addImage = false;

		// Add an image
		if ($objProduct->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objProduct->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objProduct->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrProduct = $objProduct->row();

				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrProduct['size'] = $this->imgSize;
					}
				}

				$arrProduct['singleSRC'] = $objModel->path;
				$strLightboxId = 'lightbox[lb' . $objProduct->id . ']';
				$arrProduct['fullsize'] = $this->fullsize;
				$this->addImageToTemplate($objTemplate, $arrProduct, null, $strLightboxId);
			}
		}

		$objElement = \ContentModel::findPublishedByPidAndTable($objProduct->id, 'tl_product');

		if ($objElement !== null)
		{
			while ($objElement->next())
			{
				$objTemplate->text .= $this->getContentElement($objElement->current());
			}

			$objTemplate->link        = $this->generateProductUrl($objProduct, $blnAddCategory);
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

			$arrProducts[] = $this->parseProduct($objProduct, $blnAddCategory, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % $this->product_perRow) == 0) ? ' last_col' : '') . ((($count % $this->product_perRow) == 1) ? ' first_col' : ''), $count);
		}

    $arrProducts = array_chunk($arrProducts,$this->product_perRow);

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
	 * Return the meta fields of a news article as array
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

				case 'code':
					if ($objProduct->code) 
						$return['code'] = $GLOBALS['TL_LANG']['MSC']['code_text'] .' '. $objProduct->code;
					break;

				case 'model':
					if ($objProduct->model) 
						$return['model'] = $GLOBALS['TL_LANG']['MSC']['model_text'] .' '. $objProduct->model;
					break;

				case 'brand':
					if ($objProduct->brand) 
						$return['brand'] = $GLOBALS['TL_LANG']['MSC']['brand_text'] .' '. $objProduct->brand;
					break;
				
				case 'sku':
					if ($objProduct->sku) 
						$return['sku'] = $GLOBALS['TL_LANG']['MSC']['sku_text'] .' '. $objProduct->sku;
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
		$objTemplate->meta_code = $arrMeta['code'];
		$objTemplate->meta_sku = $arrMeta['sku'];
		$objTemplate->meta_buy = $arrMeta['buy'];

		$objTemplate->hasMetaFields = !empty($arrMeta);
		$objTemplate->timestamp = $objProduct->date;
		$objTemplate->datetime = date('Y-m-d\TH:i:sP', $objProduct->date);
		$objTemplate->addImage = false;
		// Add an image
		if ($objProduct->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objProduct->singleSRC);
			if ($objModel === null)
			{
				if (!\Validator::isUuid($objProduct->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrProduct = $objProduct->row();
				// Override the default image size
				if ($this->related_imgSize != '')
				{
					$size = deserialize($this->related_imgSize);
					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrProduct['size'] = $this->related_imgSize;
					}
				}
				$arrProduct['singleSRC'] = $objModel->path;
				$strLightboxId = 'lightbox[lb' . $objProduct->id . ']';
				$arrProduct['fullsize'] = false;
				$this->addImageToTemplate($objTemplate, $arrProduct,null, $strLightboxId);
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

			$arrProducts[] = $this->parseRelated($objProduct, $blnAddCategory, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % $this->related_perRow) == 0) ? ' last_col' : '') . ((($count % $this->related_perRow) == 1) ? ' first_col' : ''), $count);
		}

		$arrProducts = array_chunk($arrProducts,$this->related_perRow);

		return $arrProducts;
	}

}
