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
 * Run in a custom namespace, so the class can be replaced
 */
namespace Respinar\ProductsBundle\Helper;

use Contao\Config;

/**
 * Class Product
 *
 * Provide methods regarding product catalogs.
 * @copyright  Hamid Abbaszadeh 2005-2014
 * @author     Hamid Abbaszadeh <https://contao.org>
 * @package    Catalog
 */
class ProductHelper
{

	/**
	 * Return the link of a product
	 * @param object
	 * @param string
	 * @param string
	 * @return string
	 */
	static function getLink($objItem, $strUrl)
	{
		// Link to the default page
		return sprintf($strUrl, (($objItem->alias != '' && !Config::get('disableAlias')) ? $objItem->alias : $objItem->id));
	}
}
