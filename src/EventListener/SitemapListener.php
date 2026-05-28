<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\EventListener;

use Contao\CoreBundle\Event\SitemapEvent;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Database;
use Contao\PageModel;
use Respinar\ProductsBundle\Model\CatalogModel;
use Respinar\ProductsBundle\Model\ProductModel;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener]
class SitemapListener
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Security $security,
        private readonly ContentUrlGenerator $urlGenerator,
    ) {
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

        if ($isMember = $this->security->isGranted('ROLE_MEMBER')) {
            // Get all catalogs
            $objCatalogs = $this->framework->getAdapter(CatalogModel::class)->findAll();
        } else {
            // Get all unprotected catalogs
            $objCatalogs = $this->framework->getAdapter(CatalogModel::class)->findByProtected('');
        }

        if (null === $objCatalogs) {
            return;
        }

        // Walk through each catalog
        foreach ($objCatalogs as $objCatalog) {
            // Skip catalogs without target page
            if (!$objCatalog->jumpTo) {
                continue;
            }

            // Skip catalogs outside the root nodes
            if (!\in_array($objCatalog->jumpTo, $arrRoot, true)) {
                continue;
            }

            if ($isMember && $objCatalog->protected && !$this->security->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, $objCatalog->groups)) {
                continue;
            }

            $objParent = $this->framework->getAdapter(PageModel::class)->findWithDetails($objCatalog->jumpTo);

            // The target page does not exist
            if (!$objParent) {
                continue;
            }

            // The target page has not been published (see #5520)
            if (!$objParent->published || ($objParent->start && $objParent->start > $time) || ($objParent->stop && $objParent->stop <= $time)) {
                continue;
            }

            // The target page is protected (see #8416)
            if ($objParent->protected && !$this->security->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, $objParent->groups)) {
                continue;
            }

            // The target page is exempt from the sitemap (see #6418)
            if ('noindex,nofollow' === $objParent->robots) {
                continue;
            }

            // Get the products
            $objProducts = $this->framework->getAdapter(ProductModel::class)->findPublishedDefaultByPid($objCatalog->id);

            if (null === $objProducts) {
                continue;
            }

            foreach ($objProducts as $objProduct) {
                try {
                    $arrPages[] = $this->urlGenerator->generate($objProduct, [], UrlGeneratorInterface::ABSOLUTE_URL);
                } catch (ExceptionInterface) {
                }
            }
        }

        foreach ($arrPages as $strUrl) {
            $event->addUrlToDefaultUrlSet($strUrl);
        }
    }
}
