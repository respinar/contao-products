<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2023 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\EventListener;

use Contao\CoreBundle\Event\PreviewUrlCreateEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Respinar\ProductsBundle\Model\ProductModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsEventListener('contao.preview_url_create')]
class PreviewUrlCreateListener
{
    private RequestStack $requestStack;
    private ContaoFramework $framework;

    public function __construct(RequestStack $requestStack, ContaoFramework $framework)
    {
        $this->requestStack = $requestStack;
        $this->framework = $framework;
    }

    public function __invoke(PreviewUrlCreateEvent $event): void
    {
        // Do something
        if (!$this->framework->isInitialized() || 'products' !== $event->getKey()) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('The request stack did not contain a request');
        }

        // Return on the product category list page
        if ('tl_product' === $request->query->get('table') && !$request->query->has('act')) {
            return;
        }

        if ((!$id = $this->getId($event, $request)) || (!$productModel = $this->getProductModel($id))) {
            return;
        }

        $event->setQuery('product='.$productModel->id);
    }

    /**
     * @return int|string
     */
    private function getId(PreviewUrlCreateEvent $event, Request $request)
    {
        // Overwrite the ID if the news settings are edited
        if ('tl_product' === $request->query->get('table') && 'edit' === $request->query->get('act')) {
            return $request->query->get('id');
        }

        return $event->getId();
    }

    /**
     * @param int|string $id
     */
    private function getProductModel($id): ?ProductModel
    {
        return $this->framework->getAdapter(ProductModel::class)->findByPk($id);
    }
}
