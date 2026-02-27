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
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Respinar\ProductsBundle\Model\ProductModel;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener('contao.preview_url_convert')]
class PreviewUrlConvertListener
{
    // private ContaoFramework $framework;

    public function __construct(
        private ContaoFramework $framework,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
    }

    public function __invoke(PreviewUrlConvertEvent $event): void
    {
        // Do something
        if (!$this->framework->isInitialized()) {
            return;
        }

        if (!($product = $this->getProductModel($event->getRequest())) instanceof ProductModel) {
            return;
        }

        $event->setUrl($this->contentUrlGenerator->generate($product, [], UrlGeneratorInterface::ABSOLUTE_PATH));
    }

    private function getProductModel(Request $request): ProductModel|null
    {
        if (!$request->query->has('product')) {
            return null;
        }

        return $this->framework->getAdapter(ProductModel::class)->findById($request->query->get('product'));
    }
}
