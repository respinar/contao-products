<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   product
 * @author    Hamid Abbaszadeh
 * @license   LGPL-3.0+
 * @copyright 2014-2016
 */


/**
 * Namespace
 */
namespace Respinar\ProductsBundle\Controller\FrontendModule;

use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\ProductCatalogModel;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Date;
use Contao\Input;
use Contao\Config;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsFrontendModule(category: 'products_modules', template: 'mod_product_list')]
class ProductListController extends AbstractFrontendModuleController
{

	public const TYPE = 'product_list';

    protected ?PageModel $page;

    /**
     * This method extends the parent __invoke method,
     * its usage is usually not necessary.
     */
    public function __invoke(Request $request, ModuleModel $model, string $section, array $classes = null, PageModel $page = null): Response
    {
        // Get the page model
        $this->page = $page;

        $scopeMatcher = $this->container->get('contao.routing.scope_matcher');

        if ($this->page instanceof PageModel && $scopeMatcher->isFrontendRequest($request)) {
            $this->page->loadDetails();
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        
        return $template->getResponse();
    }


	/**
	 * Generate the module
	 */
	// protected function xcompile()
	// {

	// 	$offset = intval($this->skipFirst);
	// 	$limit = null;

	// 	// Maximum number of items
	// 	if ($this->numberOfItems > 0)
	// 	{
	// 		$limit = $this->numberOfItems;
	// 	}

	// 	// Handle featured product
	// 	if ($this->product_featured == 'featured_product')
	// 	{
	// 		$blnFeatured = true;
	// 	}
	// 	elseif ($this->product_featured == 'unfeatured_product')
	// 	{
	// 		$blnFeatured = false;
	// 	}
	// 	else
	// 	{
	// 		$blnFeatured = null;
	// 	}

	// 	$this->Template->products = array();
	// 	$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyCatalog'];

	// 	$intTotal = ProductModel::countPublishedByPids($this->product_catalogs,$blnFeatured);

	// 	if ($intTotal < 1)
	// 	{
	// 		return;
	// 	}

	// 	$total = $intTotal - $offset;


	// 	// Split the results
	// 	if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
	// 	{
	// 		// Adjust the overall limit
	// 		if (isset($limit))
	// 		{
	// 			$total = min($limit, $total);
	// 		}

	// 		// Get the current page
	// 		$id = 'page_n' . $this->id;
	// 		$page = Input::get($id) ?: 1;

	// 		// Do not index or cache the page if the page number is outside the range
	// 		if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
	// 		{
	// 			global $objPage;
	// 			$objPage->noSearch = 1;
	// 			$objPage->cache = 0;

	// 			// Send a 404 header
	// 			header('HTTP/1.1 404 Not Found');
	// 			return;
	// 		}

	// 		// Set limit and offset
	// 		$limit = $this->perPage;
	// 		$offset += (max($page, 1) - 1) * $this->perPage;
	// 		$skip = intval($this->skipFirst);

	// 		// Overall limit
	// 		if ($offset + $limit > $total + $skip)
	// 		{
	// 			$limit = $total + $skip - $offset;
	// 		}

	// 		// Add the pagination menu
	// 		$objPagination = new \Pagination($total, $this->perPage, Config::get('maxPaginationLinks'), $id);
	// 		$this->Template->pagination = $objPagination->generate("\n  ");
	// 	}

	// 	$arrOptions = array();
	// 	if ($this->product_sortBy)
	// 	{
	// 		switch ($this->product_sortBy)
	// 		{
	// 			case 'title_asc':
	// 				$arrOptions['order'] = "title ASC";
	// 				break;
	// 			case 'title_desc':
	// 				$arrOptions['order'] = "title DESC";
	// 				break;
	// 			case 'date_asc':
	// 				$arrOptions['order'] = "tstamp ASC";
	// 				break;
	// 			case 'date_desc':
	// 				$arrOptions['order'] = "tstamp DESC";
	// 				break;
	// 			case 'custom':
	// 				$arrOptions['order'] = "sorting ASC";
	// 				break;
	// 		}
	// 	}

	// 	// Get the items
	// 	if (isset($limit))
	// 	{
	// 		$objProducts = ProductModel::findPublishedByPids($this->product_catalogs, $blnFeatured, $limit, $offset, $arrOptions);
	// 	}
	// 	else
	// 	{
	// 		$objProducts = ProductModel::findPublishedByPids($this->product_catalogs, $blnFeatured, 0, $offset, $arrOptions);
	// 	}


	// 	// Add the Products
	// 	if ($objProducts !== null)
	// 	{
	// 		$this->Template->products = $this->parseProducts($objProducts);
	// 	}

	// 	$this->Template->categories = $this->product_catalogs;

	// }
}
