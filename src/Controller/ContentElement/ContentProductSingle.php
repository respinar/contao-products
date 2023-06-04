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
namespace Respinar\ProductsBundle\Controller\ContentElement;

use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\ProductCatalogModel;

use Respinar\ProductsBundle\Helper\ProductParser;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'products_elements', template: 'ce_product')]
class ContentProductSingle extends AbstractContentElementController
{

	public const TYPE = 'product_single';

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {

		
        
		
		
		$template->text = $model->text;

        return $template->getResponse();
    }
}
