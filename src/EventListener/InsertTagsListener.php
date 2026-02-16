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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Psr\Log\LoggerInterface;
use Respinar\ProductsBundle\Model\ProductModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsHook('replaceInsertTags')]
class InsertTagsListener
{
    private const SUPPORTED_TAGS = [
        'product_url',
    ];

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly LoggerInterface $logger,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
    }

    public function __invoke(
        string $tag,
        bool $useCache,
        mixed $cacheValue,
        array $flags,
    ): string|false {
        $elements = explode('::', $tag);
        $key = strtolower($elements[0]);

        if (!\in_array($key, self::SUPPORTED_TAGS, true)) {
            return false;
        }

        return $this->replaceProductInsertTags(
            $key,
            $elements[1] ?? '',
            [...$flags, ...\array_slice($elements, 2)],
        );
    }

    private function replaceProductInsertTags(
        string $insertTag,
        string $idOrAlias,
        array $arguments,
    ): string {
        $this->framework->initialize();

        $productModel = $this->framework
            ->getAdapter(ProductModel::class)
            ->findByIdOrAlias($idOrAlias);

        if (null === $productModel) {
            return '';
        }

        return match ($insertTag) {
            'product_url' => $this->contentUrlGenerator->generate($productModel,[],UrlGeneratorInterface::ABSOLUTE_PATH) ?: './',            

            default => '',
        };
    }
}