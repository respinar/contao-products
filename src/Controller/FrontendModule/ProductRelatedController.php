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
use Contao\PageModel;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Respinar\ProductsBundle\Product\ProductParser;
use Respinar\ProductsBundle\Model\ProductModel;


#[AsFrontendModule(category: "products")]
class ProductRelatedController extends AbstractFrontendModuleController
{
	public const TYPE = 'product_related';

	public function __construct(
      private readonly ProductParser $productParser,
  ) {
  }

	protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
	{
		// Return an empty string if "auto_item" is not set to combine list and reader on same page
		if (Input::get('auto_item') === null)
		{
			return '';
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
				$template->relateds = $this->productParser->parseProducts($objRelated, $model);
			}
		}

		return $template->getResponse();
	}	
}
