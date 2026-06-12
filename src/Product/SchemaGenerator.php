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
use Contao\Environment;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
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

        if ($image = $this->generateImageObject($product->singleSRC ?? null)) {
            $jsonLd['image'] = $image;
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

    /**
     * Build a schema.org ImageObject from a Contao file UUID (singleSRC).
     */
    private function generateImageObject(mixed $uuid): ?array
    {
        if (!$uuid || ($file = FilesModel::findByUuid($uuid)) === null) {
            return null;
        }

        $projectDir = System::getContainer()->getParameter('kernel.project_dir');
        $absolutePath = $projectDir.'/'.$file->path;

        if (!is_file($absolutePath)) {
            return null;
        }

        $url = Environment::get('url').'/'.System::urlEncode($file->path);

        $imageObject = [
            '@type' => 'ImageObject',
            '@id' => $url.'#/schema/image/'.$file->id,
            'url' => $url,
            'contentUrl' => $url,
        ];

        $size = @getimagesize($absolutePath);
        if (\is_array($size) && isset($size[0], $size[1])) {
            $imageObject['width'] = $size[0];
            $imageObject['height'] = $size[1];
        }

        if ($file->extension) {
            $mime = match (strtolower($file->extension)) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'avif' => 'image/avif',
                default => null,
            };

            if ($mime) {
                $imageObject['encodingFormat'] = $mime;
            }
        }

        $meta = StringUtil::deserialize($file->meta, true);
        $lang = $GLOBALS['TL_LANGUAGE'] ?? array_key_first($meta) ?? null;
        $metaEntry = ($lang && isset($meta[$lang])) ? $meta[$lang] : (reset($meta) ?: []);

        if (!empty($metaEntry['title'])) {
            $imageObject['name'] = $metaEntry['title'];
        }
        if (!empty($metaEntry['caption'])) {
            $imageObject['caption'] = $metaEntry['caption'];
        }
        if (!empty($metaEntry['alt'])) {
            $imageObject['description'] = $metaEntry['alt'];
        }

        if (!empty($file->license)) {
            $imageObject['license'] = $file->license;
        }

        return $imageObject;
    }
}
