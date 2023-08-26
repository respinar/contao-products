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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Framework\ContaoFramework;
use Psr\Log\LoggerInterface;
use Contao\StringUtil;

use Respinar\ProductsBundle\Model\ProductModel;
use Respinar\ProductsBundle\Controller\Product;

#[AsHook('replaceInsertTags')]
class InsertTagsListener
{
    public const SUPPORTED_TAGS = [
        'product_url'
    ];

    public function __construct(private readonly ContaoFramework $framework, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(string $tag, bool $useCache, $cacheValue, array $flags): string|false
    {
        $elements = explode('::', $tag);
        $key = strtolower($elements[0]);

        if (\in_array($key, self::SUPPORTED_TAGS, true)) {
            return $this->replaceProductInsertTags($key, $elements[1], [...$flags, ...\array_slice($elements, 2)]);
        }

        return false;
    }

    private function replaceProductInsertTags (string $insertTag, string $idOrAlias, array $arguments): string
    {
        $this->framework->initialize();

        $adapter = $this->framework->getAdapter(ProductModel::class);

        if (!$model = $adapter->findByIdOrAlias($idOrAlias)) {
            return '';
        }

        $product = $this->framework->getAdapter(Product::class);

        return match ($insertTag) {
            'product_url' => $product->generateProductUrl($model, false, \in_array('absolute', $arguments, true)) ?: './',
            default => '',
        };
    }
}
