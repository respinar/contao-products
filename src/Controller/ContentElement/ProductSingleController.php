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

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\System;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Respinar\ProductsBundle\Product;
use Respinar\ProductsBundle\Model\ProductModel;


#[AsContentElement(category: "products", template: 'ce_product_single')]
class ProductSingleController extends AbstractContentElementController
{

	public const TYPE = 'product_single';

	protected function getResponse(Template $template, ContentModel $model, Request $request): Response
  {
		$objProduct = ProductModel::findOneByID($model->product);


        $model->imgSize = $model->size;

        if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create('')))
        {
          $model->imgSize = 'a:3:{i:0;s:3:"100";i:1;s:3:"100";i:2;s:13:"center_center";}';
          $model->product_template = 'product_simple';
        }

        $template->product = Product::parseProduct($objProduct, $model);

        return $template->getResponse();
	}

}
