<?php

declare(strict_types=1);

namespace Respinar\ProductsBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Config;
use Contao\PageModel;
use Contao\ContentModel;
use Contao\Environment;
use Contao\Controller;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\ProductCatalogModel;
use Respinar\ProductsBundle\Helper\ProductHelper;

#[AsHook('replaceInsertTags')]
class InsertTagsListener
{
    public const TAG = 'product_url';

    public function __invoke(string $insertTag, bool $useCache, string $cachedValue, array $flags, array $tags, array $cache, int $_rit, int $_cnt)
    {
        $chunks = explode('::', $insertTag);

        if (self::TAG !== $chunks[0]) {
            return false;
        }

        // Parameter angegeben?
        if (isset($chunks[1])) {
            // Get the items
            if (($objProduct = ProductModel::findPublishedByIdOrAlias($chunks[1])) === null) {
                return false;
            }

            $objCatalog  = ProductCatalogModel::findBy('id', $objProduct->pid);

            $objParent = PageModel::findWithDetails($objCatalog->jumpTo);

            // Set the domain (see #6421)
            $domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: Environment::get('host')) . TL_PATH . '/';

            // Generate the URL
            $strUrl = $domain . Controller::generateFrontendUrl($objParent->row(), ((Config::get('useAutoItem') && !Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);

            $objElement = ContentModel::findPublishedByPidAndTable($objProduct->id, 'tl_product');

            if ($objElement !== null) {
                $link = ProductHelper::getLink($objProduct, $strUrl);
            }

            return $link;
        } else {
            return '';
        }
    }
}
