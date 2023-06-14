<?php

declare(strict_types=1);

namespace Respinar\ProductsBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Config;
use Contao\Environment;
use Contao\PageModel;
use Contao\ContentModel;
use Contao\Controller;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\ProductCatalogModel;
use Respinar\ProductsBundle\Helper\ProductHelper;

#[AsHook('getSearchablePages')]
class getSearchablePagesListener
{
    public function __invoke(array $pages, int $rootId = null, bool $isSitemap = false, string $language = null): array
    {
        $arrRoot = array();

		if ($rootId > 0)
		{
			//$arrRoot = $this->Database->getChildRecords($rootId, 'tl_page');
		}

		$time = time();
		$arrProcessed = array();

		// Get all catalog categories
		$objCatalog = ProductCatalogModel::findByProtected('');

		// Walk through each catalog
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
					$objParent = PageModel::findWithDetails($objCatalog->jumpTo);

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
					if ($isSitemap && $objParent->sitemap == 'map_never')
					{
						continue;
					}

					// Set the domain (see #6421)
					$domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: Environment::get('host')) . TL_PATH . '/';

					// Generate the URL
					$arrProcessed[$objCatalog->jumpTo] = $domain . Controller::generateFrontendUrl($objParent->row(), ((Config::get('useAutoItem') && !Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);
				}

				$strUrl = $arrProcessed[$objCatalog->jumpTo];

				// Get the items
				$objProduct = ProductModel::findPublishedByPid($objCatalog->id);

				if ($objProduct !== null)
				{
					while ($objProduct->next())
					{
                        $objElement = ContentModel::findPublishedByPidAndTable($objProduct->id, 'tl_product');

						if ($objElement !== null)
						{
							$arrPages[] = ProductHelper::getLink($objProduct, $strUrl);
						}
						
					}
				}
			}
		}

		return $arrPages;
    }
}
