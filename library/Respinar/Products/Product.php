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
 * Run in a custom namespace, so the class can be replaced
 */
namespace Respinar\Products;


/**
 * Class Product
 *
 * Provide methods regarding news archives.
 * @copyright  Hamid Abbaszadeh 2005-2014
 * @author     Hamid Abbaszadeh <https://contao.org>
 * @package    Catalog
 */
class Product extends \Frontend
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
		$objCatalog = \ProductCatalogModel::findByProtected('');

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
				$objProduct = \ProductModel::findPublishedByPid($objCatalog->id);

				if ($objProduct !== null)
				{
					while ($objProduct->next())
					{
                        $objElement = \ContentModel::findPublishedByPidAndTable($objProduct->id, 'tl_product');

						if ($objElement !== null)
						{
							$arrPages[] = $this->getLink($objProduct, $strUrl);
						}
						
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


	public function productURLInsertTags($strTag)
    {
        // Parameter abtrennen
        $arrSplit = explode('::', $strTag);

        if ($arrSplit[0] != 'product')
        {
            //nicht unser Insert-Tag
            return false;
        }

        // Parameter angegeben?
        if (isset($arrSplit[1]))
        {
            // Get the items
			$objProduct = \ProductModel::findPublishedByIdOrAlias($arrSplit[1]);

			$objCatalog  = \ProductCatalogModel::findBy('id',$objProduct->pid);

			$objParent = \PageModel::findWithDetails($objCatalog->jumpTo);				

			// Set the domain (see #6421)
			$domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

			// Generate the URL
			$strUrl = $domain . $this->generateFrontendUrl($objParent->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);	
	
			$objElement = \ContentModel::findPublishedByPidAndTable($objProduct->id, 'tl_product');

			if ($objElement !== null)
			{
				$link = $this->getLink($objProduct, $strUrl);
			}				

			return $link;
        }
        else
        {
            return 'Fehler! foo ohne Parameter!';
        }
    }

}
