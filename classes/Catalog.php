<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Catalog
 * @link    https://respinar.org/catalog
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace catalog;


/**
 * Class Catalog
 *
 * Provide methods regarding news archives.
 * @copyright  Hamid Abbaszadeh 2005-2014
 * @author     Hamid Abbaszadeh <https://contao.org>
 * @package    Catalog
 */
class Catalog extends \Frontend
{

	/**
	 * Add product items to the indexer
	 * @param array
	 * @param integer
	 * @param boolean
	 * @return array
	 */
	public function getSearchablePages($arrPages, $intRoot=0, $blnIsSitemap=false)
	{
		$arrRoot = array();

		if ($intRoot > 0)
		{
			$arrRoot = $this->Database->getChildRecords($intRoot, 'tl_page');
		}

		$time = time();
		$arrProcessed = array();

		// Get all catalog categories
		$objCatalog = \CatalogModel::findByProtected('');

		// Walk through each archive
		if ($objCatalog !== null)
		{
			while ($objCatalog->next())
			{
				// Skip catalog categories without target page
				if (!$objCatalog->jumpTo)
				{
					continue;
				}

				// Skip catalog categories outside the root nodes
				if (!empty($arrRoot) && !in_array($objCatalog->jumpTo, $arrRoot))
				{
					continue;
				}

				// Get the URL of the jumpTo page
				if (!isset($arrProcessed[$objCatalog->jumpTo]))
				{
					$objParent = \PageModel::findWithDetails($objCatalog->jumpTo);

					// The target page does not exist
					if ($objParent === null)
					{
						continue;
					}

					// The target page has not been published (see #5520)
					if (!$objParent->published || ($objParent->start != '' && $objParent->start > $time) || ($objParent->stop != '' && $objParent->stop < $time))
					{
						continue;
					}

					// The target page is exempt from the sitemap (see #6418)
					if ($blnIsSitemap && $objParent->sitemap == 'map_never')
					{
						continue;
					}

					// Set the domain (see #6421)
					$domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

					// Generate the URL
					$arrProcessed[$objCatalog->jumpTo] = $domain . $this->generateFrontendUrl($objParent->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);
				}

				$strUrl = $arrProcessed[$objCatalog->jumpTo];

				// Get the items
				$objProduct = \CatalogProductModel::findPublishedByPid($objCatalog->id);

				if ($objProduct !== null)
				{
					while ($objProduct->next())
					{
						$arrPages[] = $this->getLink($objProduct, $strUrl);
					}
				}
			}
		}

		return $arrPages;
	}


	/**
	 * Return the link of a product
	 * @param object
	 * @param string
	 * @param string
	 * @return string
	 */
	protected function getLink($objItem, $strUrl)
	{
		// Link to the default page
		return sprintf($strUrl, (($objItem->alias != '' && !\Config::get('disableAlias')) ? $objItem->alias : $objItem->id));
	}

	/**
	 * Translate the URL parameters using the changelanguage module hook
	 *
	 * @param	array
	 * @param	string
	 * @param	array
	 * @return	array
	 * @see		ModuleChangeLanguage::compile()
	 */
	public function translateUrlParameters($arrGet, $strLanguage, $arrRootPage)
	{
		// Set the item from the auto_item parameter
		if ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			$this->Input->setGet('items', $this->Input->get('auto_item'));
		}

		$strItem = $this->Input->get('items');

        if ($strItem != '')
        {
        	$objProduct = $this->Database->prepare("SELECT tl_catalog_product.*, tl_catalog.master FROM tl_catalog_product LEFT OUTER JOIN tl_catalog ON tl_catalog_product.pid=tl_catalog.id WHERE tl_catalog_product.id=? OR tl_catalog_product.alias=?")
        							  ->limit(1)
        							  ->execute((int)$strItem, $strItem);

        	// We found a news item!!
        	if ($objProduct->numRows)
        	{
        		$id = ($objProduct->master > 0) ? $objProduct->languageMain : $objProduct->id;
        		$objItem = $this->Database->prepare("SELECT tl_catalog_product.id, tl_catalog_product.alias FROM tl_catalog_product LEFT OUTER JOIN tl_catalog ON tl_catalog_product.pid=tl_catalog.id WHERE tl_catalog.language=? AND (tl_catalog_product.id=? OR languageMain=?)")->execute($strLanguage, $id, $id);

				if ($objItem->numRows)
				{
					$arrGet['url']['items'] = $objItem->alias ? $objItem->alias : $objItem->id;
				}
        	}
        }

		return $arrGet;
	}

}
