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
namespace Respinar\ProductsBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\ModuleModel;
use Contao\Template;
use Contao\Input;
use Contao\System;
use Contao\UserModel;
use Contao\PageModel;
use Contao\Comments;
use Contao\StringUtil;
use Contao\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Respinar\ProductsBundle\Product;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\CatalogModel;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;


#[AsFrontendModule(category: "products")]
class ProductRelatedController extends AbstractFrontendModuleController
{
	public const TYPE = 'product_related';

	protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
	{
		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			Input::setGet('items', Input::get('auto_item'));
		}		

		$objProduct = ProductModel::findPublishedByIdOrAlias(Input::get('auto_item'));

		if (null === $objProduct)
		{
			return '';
		}

		$template->referer = PageModel::findById($objProduct->getRelated('pid')->overviewPage)->getFrontendUrl();

		if ($model->overviewPage)
		{
			$template->referer = PageModel::findById($model->overviewPage)->getFrontendUrl();
		}

		$template->back = $model->customLabel ?: $GLOBALS['TL_LANG']['MSC']['newsOverview'];
		$template->relateds_headline = $GLOBALS['TL_LANG']['MSC']['relateds_headline'];

		$relatedIds = StringUtil::deserialize($objProduct->related);

		if ($relatedIds)
		{
			$objRelated = ProductModel::findPublishedByIds($relatedIds);

			if ($objRelated !== null)
			{
				$template->relateds = Product::parseProducts($objRelated, $model);
			}
		}

		return $template->getResponse();
	}	
}
