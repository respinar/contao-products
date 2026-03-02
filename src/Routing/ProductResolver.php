<?php

declare(strict_types=1);

namespace Respinar\ProductsBundle\Routing;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\Content\ContentUrlResolverInterface;
use Contao\CoreBundle\Routing\Content\ContentUrlResult;
use Contao\PageModel;
use Respinar\ProductsBundle\Model\CatalogModel;
use Respinar\ProductsBundle\Model\ProductModel;

class ProductResolver implements ContentUrlResolverInterface
{
    public function __construct(private readonly ContaoFramework $framework)
    {
    }

    public function resolve(object $content): ContentUrlResult|null
    {
        if (!$content instanceof ProductModel) {
            return null;
        }

        $catalogAdapter = $this->framework->getAdapter(CatalogModel::class);
        $pageAdapter = $this->framework->getAdapter(PageModel::class);

        $catalog = $catalogAdapter->findById($content->pid);

        if (null === $catalog || !$catalog->jumpTo) {
            return null;
        }

        return ContentUrlResult::resolve(
            $pageAdapter->findPublishedById((int) $catalog->jumpTo),
        );
    }

    public function getParametersForContent(object $content, PageModel $pageModel): array
    {
        if (!$content instanceof ProductModel) {
            return [];
        }

        return [
            'parameters' => '/'.($content->alias ?: $content->id),
        ];
    }
}
