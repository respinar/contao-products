<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Controller\FrontendModule;

use Contao\Comments;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Product\ProductParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(category: 'products', template: 'frontend_module/product_detail')]
class ProductDetailController extends AbstractFrontendModuleController
{
    public const TYPE = 'product_detail';

    public function __construct(
        private readonly ProductParser $productParser,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        // $autoItem = $request->attributes->get('auto_item');

        $autoItem = Input::get('auto_item');

        // Return an empty string if "auto_item" is not set to combine list and reader on
        // same page
        if (null === $autoItem) {
            throw new PageNotFoundException('Page not found: '.$request->getUri());
        }

        // $objProduct = ProductModel::findOneByAlias(Input::get('items'));
        $model->product_catalogs = StringUtil::deserialize($model->product_catalogs);
        $objProduct = ProductModel::findPublishedByParentAndIdOrAlias($autoItem, $model->product_catalogs);

        if (!$objProduct) {
            throw new PageNotFoundException('Page not found: '.$request->getUri());
        }

        $objCatalog = $objProduct->getRelated('pid');

        if ($model->overviewPage) {
            $template->referer = $this->contentUrlGenerator->generate(PageModel::findById($model->overviewPage));
        } elseif ($objCatalog && $objCatalog->overviewPage) {
            $template->referer = $this->contentUrlGenerator->generate(PageModel::findById($objCatalog->overviewPage));
        }

        $template->back = $model->customLabel ?: $GLOBALS['TL_LANG']['MSC']['productOverview'];
        $template->relateds_headline = $GLOBALS['TL_LANG']['MSC']['relateds_headline'];

        $responseContext = System::getContainer()->get('contao.routing.response_context_accessor')->getResponseContext();

        if ($responseContext && $responseContext->has(HtmlHeadBag::class)) {
            /** @var HtmlHeadBag $htmlHeadBag */
            $htmlHeadBag = $responseContext->get(HtmlHeadBag::class);
            $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

            if ($objProduct->pageTitle) {
                $htmlHeadBag->setTitle($objProduct->pageTitle); // Already stored decoded
            } elseif ($objProduct->title) {
                $htmlHeadBag->setTitle($objProduct->title);
            }

            if ($objProduct->description) {
                $htmlHeadBag->setMetaDescription($htmlDecoder->inputEncodedToPlainText($objProduct->description));
            }
        }

        $template->product = $this->productParser->parseProduct($objProduct, $model);

        // Comments
        $bundles = System::getContainer()->getParameter('kernel.bundles');
        $objCatalog = $objProduct->getRelated('pid');

        if (isset($bundles['ContaoCommentsBundle']) && $objCatalog->allowComments) {
            $template->allowComments = true;

            // Adjust the comments headline level intHl = min((int) str_replace('h', '',
            // $model->hl), 5); template->hlc = 'h' . ($intHl + 1);

            $com_headline = StringUtil::deserialize($model->product_comHeadline);
            $template->hlc = $com_headline['unit'];
            $template->hlcText = $com_headline['value'];

            $objComment = new Comments();

            $arrNotifies = [];

            // Notify the system administrator
            if ('notify_author' !== $objCatalog->notify) {
                $arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
            }

            $objConfig = new \stdClass();

            $objConfig->perPage = $objCatalog->perPage;
            $objConfig->order = $objCatalog->sortOrder;
            $objConfig->template = $model->com_template;
            $objConfig->requireLogin = $objCatalog->requireLogin;
            $objConfig->disableCaptcha = $objCatalog->disableCaptcha;
            $objConfig->bbcode = $objCatalog->bbcode;
            $objConfig->moderate = $objCatalog->moderate;

            // Comments::addCommentsToTemplate() requires a legacy FrontendTemplate, so we
            // let it populate a throw-away FrontendTemplate and copy the rendered data onto
            // our Twig-based fragment template.
            $objCommentTemplate = new FrontendTemplate();

            $objComment->addCommentsToTemplate($objCommentTemplate, $objConfig, 'tl_product', $objProduct->id, $arrNotifies);

            // Merge the comment data (comments, pagination, form fields, ...) into the
            // fragment template, keeping the values already set by this controller.
            $template->setData(array_replace_recursive($objCommentTemplate->getData(), $template->getData()));
        } else {
            $template->allowComments = false;
        }

        return $template->getResponse();
    }
}
