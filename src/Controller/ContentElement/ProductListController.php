<?php

declare(strict_types=1);

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
namespace Respinar\ProductsBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\StringUtil;
use Contao\Template;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Respinar\ProductsBundle\Controller\Product;
use Respinar\ProductsBundle\Model\ProductModel;


#[AsContentElement(category: "products")]
class ProductListController extends AbstractContentElementController
{

	public const TYPE = 'product_list';

	protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {

		$objProducts = ProductModel::findMultipleByIds(StringUtil::deserialize($model->products));

        $model->imgSize = $model->size;

		$arrProducts = [];

		foreach($objProducts as $objProduct) {
			$arrProducts[] = Product::parseProduct($objProduct, $model);
		}

		$template->products = $arrProducts;

        return $template->getResponse();
	}

}
