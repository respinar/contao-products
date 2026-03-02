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

use Contao\Date;
use Contao\StringUtil;

final class MetaGenerator
{
    /**
     * Return the meta fields of a product.
     */
    public function generate(object $product): array
    {
        global $objPage;

        $return = [];

        $return['datetime'] = date('Y-m-d\TH:i:sP', $product->date);
        $return['date'] = Date::parse($objPage->datimFormat, $product->date);

        $price = StringUtil::deserialize($product->price);

        if (!empty($price['value'])) {
            $return['price'] = [
                'value' => $price['value'],
                'unit' => $price['unit'],
                'symbol' => $GLOBALS['TL_LANG']['MSC'][$price['unit']],
            ];

            $return['price_text'] = $GLOBALS['TL_LANG']['MSC']['price_text'];
        }

        if (isset($product->availability)) {
            $return['availability'] = [
                'class' => $product->availability,
                'value' => $GLOBALS['TL_LANG']['MSC'][$product->availability],
            ];

            $return['status_text'] = $GLOBALS['TL_LANG']['MSC']['status_text'];
        }

        $globalId = StringUtil::deserialize($product->global_ID);

        if (!empty($globalId['value'])) {
            $globalId['name'] = $GLOBALS['TL_LANG']['MSC'][$globalId['unit']];
            $return['global_ID'] = $globalId;
        }

        if (isset($product->model)) {
            $return['model'] = $product->model;
            $return['model_text'] = $GLOBALS['TL_LANG']['MSC']['model_text'];
        }

        if (isset($product->brand)) {
            $return['brand'] = $product->brand;
            $return['brand_text'] = $GLOBALS['TL_LANG']['MSC']['brand_text'];
        }

        if (isset($product->sku)) {
            $return['sku'] = $product->sku;
            $return['sku_text'] = $GLOBALS['TL_LANG']['MSC']['sku_text'];
        }

        if (isset($product->url)) {
            $return['buy'] = $product->url;
        }

        return $return;
    }
}
