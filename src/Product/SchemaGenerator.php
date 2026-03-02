<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2026 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Product;

use Contao\StringUtil;
use Contao\System;

final class SchemaGenerator
{
    /**
     * Return the schema.org data from a product.
     */
    public function generate(object $product): array
    {
        $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

        $price = StringUtil::deserialize($product->price);
        $globalId = StringUtil::deserialize($product->global_ID);

        $jsonLd = [
            '@type' => 'Product',
            'name' => $htmlDecoder->inputEncodedToPlainText($product->title),
            'description' => $product->description,
            'sku' => $product->sku,
            $globalId['unit'] => $globalId['value'],
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand,
            ],
        ];

        $jsonLd['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $product->rating_value,
            'reviewCount' => $product->rating_count,
        ];

        $jsonLd['offers'] = [
            '@type' => 'Offer',
            'priceCurrency' => $price['unit'],
            'price' => $price['value'],
            'priceValidUntil' => date('Y-m-d\TH:i:sP', $product->priceValidUntil),
            'availability' => 'http://schema.org/'.$product->availability,
            'seller' => [
                '@type' => 'Organization',
                'name' => $product->brand,
            ],
        ];

        return $jsonLd;
    }
}
