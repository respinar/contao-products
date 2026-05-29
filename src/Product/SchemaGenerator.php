<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Product;

use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\StringUtil;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SchemaGenerator
{
    public function __construct(
        private readonly HtmlDecoder $htmlDecoder,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
    }

    /**
     * Return the schema.org data from a product.
     */
    public function generate(object $product): array
    {
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            '@id' => $this->contentUrlGenerator->generate($product, [], UrlGeneratorInterface::ABSOLUTE_URL).'#/schema/product/'.$product->id,
            'name' => $this->htmlDecoder->inputEncodedToPlainText((string) $product->title),
        ];

        if ($product->description) {
            $jsonLd['description'] = $product->description;
        }

        if ($product->sku) {
            $jsonLd['sku'] = $product->sku;
        }

        $globalId = StringUtil::deserialize($product->global_ID);

        if (isset($globalId['unit'], $globalId['value']) && '' !== $globalId['value']) {
            $jsonLd[$globalId['unit']] = $globalId['value'];
        }

        if ($product->brand) {
            $jsonLd['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand,
            ];
        }

        if (null !== $product->rating_value && null !== $product->rating_count) {
            $jsonLd['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->rating_value,
                'reviewCount' => $product->rating_count,
            ];
        }

        $price = StringUtil::deserialize($product->price);

        if (isset($price['unit'], $price['value']) && '' !== $price['value']) {
            $jsonLd['offers'] = [
                '@type' => 'Offer',
                'priceCurrency' => $price['unit'],
                'price' => $price['value'],
                'priceValidUntil' => date('Y-m-d\TH:i:sP', (int) $product->priceValidUntil),
                'availability' => 'https://schema.org/'.$product->availability,
            ];

            if ($product->brand) {
                $jsonLd['offers']['seller'] = [
                    '@type' => 'Organization',
                    'name' => $product->brand,
                ];
            }
        }

        return $jsonLd;
    }
}
