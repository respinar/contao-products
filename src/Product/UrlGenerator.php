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

use Contao\Config;
use Contao\Environment;
use Contao\PageModel;
use Contao\StringUtil;

final class UrlGenerator
{
    /**
     * URL cache.
     *
     * @var array<string, ?string>
     */
    private static array $urlCache = [];

    /**
     * Generate a product URL.
     */
    public static function generate(
        object $product,
        bool $addCategory = false,
        bool $absolute = false,
    ): string {
        $cacheKey = 'id_'.$product->id.($absolute ? '_absolute' : '');

        // Load from cache
        if (isset(self::$urlCache[$cacheKey])) {
            return self::$urlCache[$cacheKey];
        }

        self::$urlCache[$cacheKey] = null;

        $page = PageModel::findByPk($product->getRelated('pid')->jumpTo);

        if (!$page instanceof PageModel) {
            self::$urlCache[$cacheKey] = StringUtil::ampersand(Environment::get('requestUri'));
        } else {
            $params = '/'.($product->alias ?: $product->id);

            self::$urlCache[$cacheKey] = StringUtil::ampersand(
                $absolute
                    ? $page->getAbsoluteUrl($params)
                    : $page->getFrontendUrl($params)
            );

            /*
            // Legacy implementation
            self::$urlCache[$cacheKey] = StringUtil::ampersand(
                Controller::generateFrontendUrl(
                    $page->row(),
                    ((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/' : '/items/')
                    . ((!Config::get('disableAlias') && $product->alias !== '') ? $product->alias : $product->id)
                )
            );
            */
        }

        return self::$urlCache[$cacheKey];
    }

    /**
     * Generate a HTML link.
     */
    public static function generateLink(
        string $link,
        object $product,
        bool $addCategory = false,
        bool $readMore = false,
    ): string {
        return sprintf(
            '<a href="%s" title="%s">%s%s</a>',
            self::generate($product, $addCategory),
            StringUtil::specialchars(
                sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $product->title),
                true
            ),
            $link,
            $readMore ? ' <span class="invisible">'.$product->title.'</span>' : ''
        );
    }

    /**
     * Return the product link.
     */
    public static function getLink(
        object $product,
        string $url,
    ): string {
        return sprintf(
            $url,
            ($product->alias !== '' && !Config::get('disableAlias'))
                ? $product->alias
                : $product->id
        );
    }
}