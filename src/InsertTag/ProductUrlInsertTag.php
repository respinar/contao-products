<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\InsertTag;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Respinar\ProductsBundle\Model\ProductModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsInsertTag('product_url')]
class ProductUrlInsertTag
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        $this->framework->initialize();

        $idOrAlias = $insertTag->getParameters()->get(0);

        if (null === $idOrAlias) {
            return new InsertTagResult('');
        }

        $productModel = $this->framework
            ->getAdapter(ProductModel::class)
            ->findPublishedByIdOrAlias($idOrAlias)
        ;

        if (null === $productModel) {
            return new InsertTagResult('');
        }

        $url = $this->contentUrlGenerator->generate(
            $productModel,
            [],
            UrlGeneratorInterface::ABSOLUTE_PATH,
        );

        return new InsertTagResult($url ?: './');
    }
}
