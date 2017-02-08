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
namespace Respinar\Products;


/**
 * Class ModuleCatalogList
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModuleProductList extends \ModuleProduct
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_product_list';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['product_list'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->product_catalogs = $this->sortOutProtected(deserialize($this->product_catalogs));

		// No catalog available
		if (!is_array($this->product_catalogs) || empty($this->product_catalogs))
		{
			return '';
		}

		// Show the catalog detail if an item has been selected
		if ($this->product_detailModule > 0 && (isset($_GET['items']) || ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))))
		{
			return $this->getFrontendModule($this->product_detailModule, $this->strColumn);
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		$offset = intval($this->skipFirst);
		$limit = null;

		// Maximum number of items
		if ($this->numberOfItems > 0)
		{
			$limit = $this->numberOfItems;
		}

		// Handle featured news
		if ($this->product_featured == 'featured_product')
		{
			$blnFeatured = true;
		}
		elseif ($this->product_featured == 'unfeatured_product')
		{
			$blnFeatured = false;
		}
		else
		{
			$blnFeatured = null;
		}

		$this->Template->products = array();
		$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyCatalog'];

		$intTotal = \ProductModel::countPublishedByPids($this->product_catalogs,$blnFeatured);

		if ($intTotal < 1)
		{
			return;
		}

		$total = $intTotal - $offset;


		// Split the results
		if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
		{
			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = 'page_n' . $this->id;
			$page = \Input::get($id) ?: 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				global $objPage;
				$objPage->noSearch = 1;
				$objPage->cache = 0;

				// Send a 404 header
				header('HTTP/1.1 404 Not Found');
				return;
			}

			// Set limit and offset
			$limit = $this->perPage;
			$offset += (max($page, 1) - 1) * $this->perPage;
			$skip = intval($this->skipFirst);

			// Overall limit
			if ($offset + $limit > $total + $skip)
			{
				$limit = $total + $skip - $offset;
			}

			// Add the pagination menu
			$objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}

		$arrOptions = array();
		if ($this->product_sortBy)
		{
			switch ($this->product_sortBy)
			{
				case 'title_asc':
					$arrOptions['order'] = "title ASC";
					break;
				case 'title_desc':
					$arrOptions['order'] = "title DESC";
					break;
				case 'date_asc':
					$arrOptions['order'] = "date ASC";
					break;
				case 'date_desc':
					$arrOptions['order'] = "date DESC";
					break;
				case 'custom':
					$arrOptions['order'] = "sorting ASC";
					break;
			}
		}

		// Get the items
		if (isset($limit))
		{
			$objProducts = \ProductModel::findPublishedByPids($this->product_catalogs, $blnFeatured, $limit, $offset, $arrOptions);
		}
		else
		{
			$objProducts = \ProductModel::findPublishedByPids($this->product_catalogs, $blnFeatured, 0, $offset, $arrOptions);
		}


		// Add the Products
		if ($objProducts !== null)
		{
			$this->Template->products = $this->parseProducts($objProducts);
		}

		$this->Template->categories = $this->product_catalogs;

	}
}
