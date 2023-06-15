<?php

declare(strict_types=1);

namespace Respinar\ProductsBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsHook('initializeSystem')]
class InitializeSystemListener
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ScopeMatcher $scopeMatcher,
        private readonly Packages $packages,
    ) {
    }

    /**
     * Load the CSS file for the back end navigation group icon.
     */
    public function __invoke(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request || !$this->scopeMatcher->isBackendRequest($request)) {
            return;
        }

        //$GLOBALS['TL_JAVASCRIPT'][] = $this->packages->getUrl('main.js', 'respinar_products');
        $GLOBALS['TL_CSS'][] = $this->packages->getUrl('css/backend.css', 'respinar_products');
    }
}