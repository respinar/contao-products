<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */


/**
 * Namespace
 */
namespace Respinar\ProductsBundle\Controller\ContentElement;

use Contao\Config;
use Contao\ContentModel;
use Contao\StringUtil;
use Contao\Template;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Input;
use Contao\Pagination;
use Contao\System;
use Respinar\ProductsBundle\Model\CatalogModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Respinar\ProductsBundle\Product;
use Respinar\ProductsBundle\Model\ProductModel;


#[AsContentElement(category: "products", template: 'ce_product_catalog')]
class ProductCatalogController extends AbstractContentElementController
{

	public const TYPE = 'product_catalog';

	protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {

		if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create('')))
        {
			$model->imgSize = 'a:3:{i:0;s:3:"100";i:1;s:3:"100";i:2;s:13:"center_center";}';
		  	$model->product_template = 'product_simple';
        }

		$template->empty = $GLOBALS['TL_LANG']['MSC']['emptyCatalog'];

		$model->product_catalogs = Product::sortOutProtected(StringUtil::deserialize($model->product_catalogs));

		$objCatalogs = CatalogModel::findMultipleByIds($model->product_catalogs);

		// No news archives available
		if (empty($objCatalogs))
		{
			return  $template->getResponse();
		}

		$offset = intval($model->skipFirst);
		$limit = null;

		// Maximum number of items
		if ($model->numberOfItems > 0)
		{
			$limit = $model->numberOfItems;
		}

		// Handle featured product
		if ($model->product_featured == 'featured_product')
		{
			$blnFeatured = true;
		}
		elseif ($model->product_featured == 'unfeatured_product')
		{
			$blnFeatured = false;
		}
		else
		{
			$blnFeatured = null;
		}

		$template->products = array();

		$intTotal = ProductModel::countPublishedByPids($model->product_catalogs, $blnFeatured);

		if ($intTotal < 1)
		{
			return $template->getResponse();
		}

		$total = $intTotal - $offset;

		// Split the results
		if ($model->perPage > 0 && (!isset($limit) || $model->numberOfItems > $model->perPage))
		{
			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = 'page_n' . $model->id;
			$page = Input::get($id) ?: 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$model->perPage), 1))
			{
				global $objPage;
				$objPage->noSearch = 1;
				$objPage->cache = 0;

				// Send a 404 header
				header('HTTP/1.1 404 Not Found');
				return $template->getResponse();
			}

			// Set limit and offset
			$limit = $model->perPage;
			$offset += (max($page, 1) - 1) * $model->perPage;
			$skip = intval($model->skipFirst);

			// Overall limit
			if ($offset + $limit > $total + $skip)
			{
				$limit = $total + $skip - $offset;
			}

			// Add the pagination menu
			$objPagination = new Pagination($total, $model->perPage, Config::get('maxPaginationLinks'), $id);
			$template->pagination = $objPagination->generate("\n  ");
		}

		$arrOptions = array();
		if ($model->product_sortBy)
		{
			switch ($model->product_sortBy)
			{
				case 'title_asc':
					$arrOptions['order'] = "title ASC";
					break;
				case 'title_desc':
					$arrOptions['order'] = "title DESC";
					break;
				case 'date_asc':
					$arrOptions['order'] = "tstamp ASC";
					break;
				case 'date_desc':
					$arrOptions['order'] = "tstamp DESC";
					break;
				case 'custom':
					$arrOptions['order'] = "sorting ASC";
					break;
			}
		}

		// Get the items
		if (isset($limit))
		{
			$objProducts = ProductModel::findPublishedByPids($model->product_catalogs, $blnFeatured, $limit, $offset, $arrOptions);
		}
		else
		{
			$objProducts = ProductModel::findPublishedByPids($model->product_catalogs, $blnFeatured, 0, $offset, $arrOptions);
		}

			// Add the Products
		if ($objProducts !== null)
		{
			$template->products = Product::parseProducts($objProducts, $model);
		}

        return $template->getResponse();
	}

}
