<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Product\ProductParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(category: 'products', template: 'frontend_module/product_related')]
class ProductRelatedController extends AbstractFrontendModuleController
{
    public const TYPE = 'product_related';

    public function __construct(
        private readonly ProductParser $productParser,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $autoItem = $request->attributes->get('auto_item') ?? $request->query->get('auto_item');

        // Return an empty response if "auto_item" is not set to combine list and reader
        // on same page
        if (null === $autoItem) {
            return new Response('');
        }

        $objProduct = ProductModel::findPublishedByIdOrAliasForPage($autoItem);

        if (!$objProduct) {
            return new Response('');
        }

        $objCatalog = $objProduct->getRelated('pid');

        if ($model->overviewPage) {
            $template->referer = $this->contentUrlGenerator->generate(PageModel::findById($model->overviewPage));
        } elseif ($objCatalog?->overviewPage) {
            $template->referer = $this->contentUrlGenerator->generate(PageModel::findById($objCatalog->overviewPage));
        }

        $template->back = $model->customLabel ?: $GLOBALS['TL_LANG']['MSC']['productOverview'];
        $template->relateds_headline = $GLOBALS['TL_LANG']['MSC']['relateds_headline'];

        $template->relateds = [];

        $relatedIds = StringUtil::deserialize($objProduct->related);

        if ($relatedIds) {
            $objRelated = ProductModel::findPublishedByIds($relatedIds);

            if (null !== $objRelated) {
                $template->relateds = $this->productParser->parseProducts($objRelated, $model);
            }
        }

        return $template->getResponse();
    }
}
