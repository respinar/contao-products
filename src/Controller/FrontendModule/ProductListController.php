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

use Contao\Config;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\Pagination;
use Contao\StringUtil;
use Respinar\ProductsBundle\Model\CatalogModel;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Product\AccessChecker;
use Respinar\ProductsBundle\Product\ProductParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(category: 'products', template: 'frontend_module/product_list')]
class ProductListController extends AbstractFrontendModuleController
{
    public const TYPE = 'products_list';

    public function __construct(private readonly ProductParser $productParser)
    {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $template->empty = $GLOBALS['TL_LANG']['MSC']['emptyCatalog'];

        $template->products = [];

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
            $page = $request->query->get($id, '1');

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
