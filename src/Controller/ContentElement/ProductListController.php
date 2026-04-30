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
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $objProducts = ProductModel::findMultipleByIds(StringUtil::deserialize($model->products));

        $model->imgSize = $model->size;

        if ($this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest() ?? Request::create(''))) {
            $model->imgSize = 'a:3:{i:0;s:3:"100";i:1;s:3:"100";i:2;s:13:"center_center";}';
            $model->product_template = 'product_simple';
        }

        $arrProducts = [];

        foreach ($objProducts as $objProduct) {
            $arrProducts[] = $this->productParser->parseProduct($objProduct, $model);
        }

        $template->products = $arrProducts;

        return $template->getResponse();
    }
}
