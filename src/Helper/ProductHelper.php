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
use Contao\System;
use Contao\StringUtil;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Respinar\ProductsBUndle\Model\ProductCatalogModel;

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
	 * Sort out protected catalogs
	 * @param array
	 * @return array
	 */
	protected function sortOutProtected($arrCatalogs)
	{
		if (empty($arrCatalogs) || !\is_array($arrCatalogs))
		{
			return $arrCatalogs;
		}

		$objCatalog = ProductCatalogModel::findMultipleByIds($arrCatalogs);
		$arrCatalogs = array();

		if ($objCatalog !== null)
		{
			$security = System::getContainer()->get('security.helper');

			while ($objCatalog->next())
			{
				if ($objCatalog->protected && !$security->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, StringUtil::deserialize($objCatalog->groups, true)))
				{
					continue;
				}

				$arrCatalogs[] = $objCatalog->id;
			}
		}

		return $arrCatalogs;
	}


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
