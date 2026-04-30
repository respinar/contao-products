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

use Contao\Config;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Pagination;
use Contao\StringUtil;
use Contao\System;
use Respinar\ProductsBundle\Model\CatalogModel;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Product\AccessChecker;
use Respinar\ProductsBundle\Product\ProductParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'products', template: 'content_element/product_catalog')]
class ProductCatalogController extends AbstractContentElementController
{
    public const TYPE = 'product_catalog';

    public function __construct(private readonly ProductParser $productParser)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create(''))) {
            $model->imgSize = 'a:3:{i:0;s:3:"100";i:1;s:3:"100";i:2;s:13:"center_center";}';
            $model->product_template = 'product_simple';
        }

        $model->imgSize = $model->size;

        $template->empty = $GLOBALS['TL_LANG']['MSC']['emptyCatalog'];

        $model->product_catalogs = AccessChecker::sortOutProtected(StringUtil::deserialize($model->product_catalogs));

        $objCatalogs = CatalogModel::findMultipleByIds($model->product_catalogs);

        // No catalogs available
        if (empty($objCatalogs)) {
            return $template->getResponse();
        }

        $offset = (int) $model->skipFirst;
        $limit = null;

        // Maximum number of items
        if ($model->numberOfItems > 0) {
            $limit = $model->numberOfItems;
        }

        // Handle featured product
        if ('featured_product' === $model->product_featured) {
            $blnFeatured = true;
        } elseif ('unfeatured_product' === $model->product_featured) {
            $blnFeatured = false;
        } else {
            $blnFeatured = null;
        }

        $template->products = [];

        $intTotal = ProductModel::countPublishedByPids($model->product_catalogs, $blnFeatured);

        if ($intTotal < 1) {
            return $template->getResponse();
        }

        $total = $intTotal - $offset;

        // Split the results
        if ($model->perPage > 0 && (!isset($limit) || $model->numberOfItems > $model->perPage)) {
            // Adjust the overall limit
            if (isset($limit)) {
                $total = min($limit, $total);
            }

            // Get the current page
            $id = 'page_n'.$model->id;
            $page = $request->query->get($id, '1') ?: 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $model->perPage), 1)) {
                $response = $template->getResponse();
                $response->setStatusCode(Response::HTTP_NOT_FOUND);

                return $response;
            }

            // Set limit and offset
            $limit = $model->perPage;
            $offset += (max($page, 1) - 1) * $model->perPage;
            $skip = (int) $model->skipFirst;

            // Overall limit
            if ($offset + $limit > $total + $skip) {
                $limit = $total + $skip - $offset;
            }

            // Add the pagination menu
            $objPagination = new Pagination($total, $model->perPage, Config::get('maxPaginationLinks'), $id);
            $template->pagination = $objPagination->generate("\n  ");
        }

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

        // Get the items
        if (isset($limit)) {
            $objProducts = ProductModel::findPublishedByPids($model->product_catalogs, $blnFeatured, $limit, $offset, $arrOptions);
        } else {
            $objProducts = ProductModel::findPublishedByPids($model->product_catalogs, $blnFeatured, 0, $offset, $arrOptions);
        }

        // Add the Products
        if (null !== $objProducts) {
            $template->products = $this->productParser->parseProducts($objProducts, $model);
        }

        return $template->getResponse();
    }
}
