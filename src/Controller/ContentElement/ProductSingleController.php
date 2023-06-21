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
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Respinar\ProductsBundle\Controller\Product;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\CatalogModel;


#[AsContentElement(category: "products")]
class ProductSingleController extends AbstractContentElementController
{

	public const TYPE = 'product_single';

	protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
		$objProduct = ProductModel::findOneByID($model->product);

        $model->imgSize = $model->size;

        $template->product = Product::parseProduct($objProduct, $model);

        return $template->getResponse();
	}

}
