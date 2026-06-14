<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Product\ProductParser;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'products', template: 'content_element/product_list')]
class ProductListController extends AbstractContentElementController
{
    public const TYPE = 'product_list';

    public function __construct(
        private readonly ProductParser $productParser,
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
        private readonly Packages $packages,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $arrOptions = [];

        if ($model->product_sortBy) {
            switch ($model->product_sortBy) {
                case 'title_asc':
                    $arrOptions['order'] = 'title ASC';
                    break;
                case 'title_desc':
                    $arrOptions['order'] = 'title DESC';
                    break;
                case 'date_asc':
                    $arrOptions['order'] = 'tstamp ASC';
                    break;
                case 'date_desc':
                    $arrOptions['order'] = 'tstamp DESC';
                    break;
                case 'custom':
                    $arrOptions['order'] = 'sorting ASC';
                    break;
            }
        }

        $objProducts = ProductModel::findPublishedMultipleByIds(StringUtil::deserialize($model->products, true), $arrOptions);

        $model->imgSize = $model->size;

        if ($this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest() ?? Request::create(''))) {
            $model->imgSize = 'a:3:{i:0;s:3:"100";i:1;s:3:"100";i:2;s:13:"center_center";}';
            $model->product_template = 'product_compact';
        }

        $arrProducts = [];

        if (null !== $objProducts) {
            $arrProducts = $this->productParser->parseProducts($objProducts, $model);
        }

        $template->products = $arrProducts;

        return $template->getResponse();
    }
}
