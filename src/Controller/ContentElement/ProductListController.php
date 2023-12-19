<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2023 <hamid@respinar.com>
 *
 * @license MIT
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
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Respinar\ProductsBundle\Product;
use Respinar\ProductsBundle\Model\ProductModel;


#[AsContentElement(category: "products", template: 'ce_product_list')]
class ProductListController extends AbstractContentElementController
{

	public const TYPE = 'product_list';

	protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {

		$objProducts = ProductModel::findMultipleByIds(StringUtil::deserialize($model->products));

        $model->imgSize = $model->size;

		if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create('')))
        {
          $model->imgSize = 'a:3:{i:0;s:3:"100";i:1;s:3:"100";i:2;s:13:"center_center";}';
		  $model->product_template = 'product_simple';
        }

		$arrProducts = [];

		foreach($objProducts as $objProduct) {
			$arrProducts[] = Product::parseProduct($objProduct, $model);
		}

		$template->products = $arrProducts;

        return $template->getResponse();
	}

}
