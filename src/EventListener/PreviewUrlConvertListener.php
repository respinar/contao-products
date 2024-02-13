<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\EventListener;

use Contao\CoreBundle\Event\PreviewUrlConvertEvent;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Respinar\ProductsBundle\Model\ProductModel;

use Respinar\ProductsBundle\Product;


#[AsEventListener('contao.preview_url_convert')]
class PreviewUrlConvertListener
{
    private ContaoFramework  $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function __invoke(PreviewUrlConvertEvent $event): void
    {
        // Do something
        if (!$this->framework->isInitialized()) {
            return;
        }

        if (null === ($product = $this->getProductModel($event->getRequest()))) {
            return;
        }

        $event->setUrl($this->framework->getAdapter(Product::class)->generateProductUrl($product, false, true));

    }

    private function getProductModel(Request $request): ?ProductModel
    {
        if (!$request->query->has('product')) {
            return null;
        }

        return $this->framework->getAdapter(ProductModel::class)->findByPk($request->query->get('product'));
    }
}
