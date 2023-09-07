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

use Respinar\ProductsBundle\Controller\Product;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Model\CatalogModel;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;


#[AsFrontendModule(category: "products")]
class ProductDetailController extends AbstractFrontendModuleController
{
	public const TYPE = 'product_detail';

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {

		if ($model->overviewPage)
		{
			$template->referer = PageModel::findById($model->overviewPage)->getFrontendUrl();
			$template->back = $model->customLabel ?: $GLOBALS['TL_LANG']['MSC']['newsOverview'];
		}
		$template->relateds_headline = $GLOBALS['TL_LANG']['MSC']['relateds_headline'];

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			Input::setGet('items', Input::get('auto_item'));
		}

        //$objProduct = ProductModel::findOneByAlias(Input::get('items'));
		$model->product_catalogs = StringUtil::deserialize($model->product_catalogs);
		$objProduct = ProductModel::findPublishedByParentAndIdOrAlias(Input::get('items'), $model->product_catalogs);

		if (null === $objProduct)
		{
			throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
		}

		// 	Update the database
		// 	$this->Database->prepare("UPDATE tl_product SET `visit`=`visit`+1 WHERE id=?")
		// 				   ->execute($objProduct->id);


		$responseContext = System::getContainer()->get('contao.routing.response_context_accessor')->getResponseContext();


		if ($responseContext && $responseContext->has(HtmlHeadBag::class))
		{

			/** @var HtmlHeadBag $htmlHeadBag */
			$htmlHeadBag = $responseContext->get(HtmlHeadBag::class);
			$htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

			if ($objProduct->pageTitle)
			{
				$htmlHeadBag->setTitle($objProduct->pageTitle); // Already stored decoded
			}
			elseif ($objProduct->title)
			{
				$htmlHeadBag->setTitle($objProduct->title);
			}

			if ($objProduct->description)
			{
				$htmlHeadBag->setMetaDescription($htmlDecoder->inputEncodedToPlainText($objProduct->description));
			}

		}

        //$objCatalog = CatalogModel::findByIdOrAlias($objProduct->pid);

        $template->product = Product::parseProduct($objProduct, $model);


		// Rerlated products
		if ($model->related_show)
		{
			$relateds = StringUtil::deserialize($objProduct->related);

			// print_r($relateds);
			// exit('STOP');

			if ($relateds) {

				$objProducts = ProductModel::findPublishedByIds($relateds);

				$template->relateds = Product::parseRelateds($objProducts, $model);
			}
		}

		// Comments
		$bundles = System::getContainer()->getParameter('kernel.bundles');
		$objCatalog = $objProduct->getRelated('pid');

		if (isset($bundles['ContaoCommentsBundle']) && $objCatalog->allowComments)
		{

			$template->allowComments = true;

			// Adjust the comments headline level
			//$intHl = min((int) str_replace('h', '', $model->hl), 5);
			//$template->hlc = 'h' . ($intHl + 1);

			$com_headline = StringUtil::deserialize($model->com_headline);	;
			$template->hlc = $com_headline['unit'];
			$template->hlcText = $com_headline['value'];

			//$template->import(Comments::class, 'Comments');

			$objComment = new Comments();

			$arrNotifies = array();

			// Notify the system administrator
			if ($objCatalog->notify != 'notify_author')
			{
				$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
			}

			// Notify the author
			if ($objCatalog->notify != 'notify_admin')
			{
				/** @var UserModel $objAuthor */
				if (($objAuthor = $objProduct->getRelated('author')) instanceof UserModel && $objAuthor->email != '')
				{
					$arrNotifies[] = $objAuthor->email;
				}
			}

			$objConfig = new \stdClass();

			$objConfig->perPage = $objCatalog->perPage;
			$objConfig->order = $objCatalog->sortOrder;
			$objConfig->template = $model->com_template;
			$objConfig->requireLogin = $objCatalog->requireLogin;
			$objConfig->disableCaptcha = $objCatalog->disableCaptcha;
			$objConfig->bbcode = $objCatalog->bbcode;
			$objConfig->moderate = $objCatalog->moderate;

			$objComment->addCommentsToTemplate($template, $objConfig, 'tl_products', $objProduct->id, $arrNotifies);
		} else {
			$template->allowComments = false;
		}


        return $template->getResponse();
	}


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	// public function generate()
	// {
	// 	if (TL_MODE == 'BE')
	// 	{
	// 		$objTemplate = new \BackendTemplate('be_wildcard');

	// 		$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['product_detail'][0]) . ' ###';
	// 		$objTemplate->title = $this->headline;
	// 		$objTemplate->id = $this->id;
	// 		$objTemplate->link = $this->name;
	// 		$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

	// 		return $objTemplate->parse();
	// 	}

	// 	// Set the item from the auto_item parameter
	// 	if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
	// 	{
	// 		\Input::setGet('items', \Input::get('auto_item'));
	// 	}

	// 	$this->product_catalogs = $this->sortOutProtected(deserialize($this->product_catalogs));

	// 	if (TL_MODE == 'FE')
	// 	{
    //         $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/products/assets/vendor/rateit/jquery.rateit.min.js|static';
    //         $GLOBALS['TL_CSS'][] = 'system/modules/products/assets/vendor/rateit/rateit.css|static';
    //     }

	// 	return parent::generate();
	// }


	/**
	 * Generate the module
	 */
	// protected function compile()
	// {

	// 	if ($objProduct->singleSRC != '')
	// 	{
	// 		$objModel = \FilesModel::findByUuid($objProduct->singleSRC);
	// 	}

	// 	$ogTagsURL = self::replaceInsertTags('{{env::path}}{{env::request}}');
	// 	$ogTagsImage = self::replaceInsertTags('{{env::path}}').$objModel->path;

	// 	$GLOBALS['TL_HEAD'][] = '<meta property="og:type" content="product" />';
	// 	$GLOBALS['TL_HEAD'][] = '<meta property="og:title" content="'.$objProduct->title.'" />';
	// 	$GLOBALS['TL_HEAD'][] = '<meta property="og:url" content="'.$ogTagsURL.'" />';
	// 	$GLOBALS['TL_HEAD'][] = '<meta property="og:image" content="'.$ogTagsImage.'" />';
}
