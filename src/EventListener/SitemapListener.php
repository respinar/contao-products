<?php

declare(strict_types=1);

namespace Respinar\ProductsBundle\EventListener;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\SitemapEvent;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;

use Contao\PageModel;
use Contao\Database;
use Contao\CoreBundle\Framework\ContaoFramework;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\CatalogModel;

/**
 * @ServiceTag("kernel.event_listener", event=ContaoCoreEvents::SITEMAP)
 */
class SitemapListener
{
	public function __construct(private readonly ContaoFramework $framework)
    {
    }

    public function __invoke(SitemapEvent $event): void
    {

        $arrRoot = $this->framework->createInstance(Database::class)->getChildRecords($event->getRootPageIds(), 'tl_page');

        // Early return here in the unlikely case that there are no pages
        if (empty($arrRoot)) {
            return;
        }

		$arrPages = [];
        $time = time();

		// Get all catalog categories
		$objCatalogs = $this->framework->getAdapter(CatalogModel::class)->findByProtected('');
		//CatalogModel::findByProtected('');

		if (null === $objCatalogs) {
            return;
        }

		// Walk through each catalog
		foreach ($objCatalogs as $objCatalog)
		{
			// Skip catalog without target page
			if (!$objCatalog->jumpTo) {
				continue;
			}

			// Skip catalog categories outside the root nodes
			if (!\in_array($objCatalog->jumpTo, $arrRoot, true)) {
				continue;
			}

			$objParent = $this->framework->getAdapter(PageModel::class)->findWithDetails($objCatalog->jumpTo);

			// The target page does not exist
            if (null === $objParent) {
                continue;
            }

            // The target page has not been published (see #5520)
            if (!$objParent->published || ($objParent->start && $objParent->start > $time) || ($objParent->stop && $objParent->stop <= $time)) {
                continue;
            }

            // The target page is protected (see #8416)
            if ($objParent->protected) {
                continue;
            }

            // The target page is exempt from the sitemap (see #6418)
            if ('noindex,nofollow' === $objParent->robots) {
                continue;
            }

			// Get the items
            $objProducts = $this->framework->getAdapter(ProductModel::class)->findPublishedDefaultByPid($objCatalog->id);

			if (null === $objProducts) {
                continue;
            }

			foreach ($objProducts as $objProduct) {
                // if ('noindex,nofollow' === $objNews->robots) {
                //     continue;
                // }

                $arrPages[] = $objParent->getAbsoluteUrl('/'.($objProduct->alias ?: $objProduct->id));
            }

		}

		$sitemap = $event->getDocument();

		foreach ($arrPages as $strUrl) {

			$urlSet = $sitemap->childNodes[0];

			$loc = $sitemap->createElement('loc', $strUrl);
			$urlEl = $sitemap->createElement('url');
			$urlEl->appendChild($loc);
			$urlSet->appendChild($urlEl);
        }

    }
}
