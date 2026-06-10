<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Model;

use Contao\Model;
use Contao\Model\Collection;
use Contao\Model\MetadataTrait;

class ProductModel extends Model
{
    use MetadataTrait;

    protected static $strTable = 'tl_product';


    /**
     * Find a published product by its ID (globally unique).
     */
    public static function findPublishedById(int $id, array $arrOptions = []): self|null
    {
        $t = static::$strTable;
        $arrColumns = ["$t.id=?"];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findOneBy($arrColumns, [$id], $arrOptions);
    }

    /**
     * Find a published product by its ID or alias.
     *
     * @param mixed $varId      The numeric ID or alias name
     * @param array $arrOptions An optional options array
     */
    public static function findPublishedByIdOrAlias($varId, array $arrOptions = []): self|null
    {
        $t = static::$strTable;
        $arrColumns = ["($t.id=? OR $t.alias=?)"];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findOneBy($arrColumns, [is_numeric($varId) ? $varId : 0, $varId], $arrOptions);
    }

    /**
     * Find a published product by alias.
     *
     * As aliases are only unique per website (root page of the catalog's reader
     * page), the same alias may exist in several catalogs, in which case the
     * first match is returned. Use findPublishedByAliasAndRootPage() to resolve
     * an alias within the current website.
     *
     * @param mixed $varAlias   The alias name
     * @param array $arrOptions An optional options array
     *
     * @return self|null
     */
    public static function findPublishedByAlias($varAlias, array $arrOptions = []): self|null
    {
        $t = static::$strTable;
        $arrColumns = ["$t.alias=?"];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findOneBy($arrColumns, $varAlias, $arrOptions);
    }

    /**
     * Find a published product by alias and root page.
     *
     * @param mixed $varAlias      The alias name
     * @param mixed $intRootPageId The root page ID
     * @param array $arrOptions    An optional options array
     */
    public static function findPublishedByAliasAndRootPage($varAlias, $intRootPageId, array $arrOptions = []): self|null
    {
        $t = static::$strTable;
        $arrColumns = ["$t.alias=? AND $t.rootPageId=?"];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findOneBy($arrColumns, [$varAlias, (int) $intRootPageId], $arrOptions);
    }

    /**
     * Find published product items by their parent ID and ID or alias.
     *
     * @param mixed $varId      The numeric ID or alias name
     * @param array $arrPids    An array of parent IDs
     * @param array $arrOptions An optional options array
     */
    public static function findPublishedByParentAndIdOrAlias($varId, $arrPids, array $arrOptions = []): self|null
    {
        if (!\is_array($arrPids) || [] === $arrPids) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = ["($t.id=? OR $t.alias=?) AND $t.pid IN(".implode(',', array_map(intval(...), $arrPids)).')'];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findOneBy($arrColumns, [is_numeric($varId) ? $varId : 0, $varId], $arrOptions);
    }

    /**
     * Find published product items by their IDs.
     *
     * @param array $arrIds      An array of product IDs
     * @param bool  $blnFeatured If true, return only featured product, if false, return only unfeatured product
     * @param int   $intLimit    An optional limit
     * @param int   $intOffset   An optional offset
     * @param array $arrOptions  An optional options array
     *
     * @return Collection<self>|self|null
     */
    public static function findPublishedByIds($arrIds, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = []): Collection|self|null
    {
        if (!\is_array($arrIds) || [] === $arrIds) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = ["$t.id IN(".implode(',', array_map(intval(...), $arrIds)).')'];

        if (true === $blnFeatured) {
            $arrColumns[] = "$t.featured=1";
        } elseif (false === $blnFeatured) {
            $arrColumns[] = "$t.featured=''";
        }

        // Never return unpublished elements in the back end, so they don't end up in the
        // RSS feed
        if (!static::isPreviewMode($arrOptions) || TL_MODE === 'BE') {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        $arrOptions['order'] ??= "$t.date DESC";

        $arrOptions['limit'] = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Find published product items by their parent ID.
     *
     * @param array $arrPids     An array of product catalogs IDs
     * @param bool  $blnFeatured If true, return only featured product, if false, return only unfeatured product
     * @param int   $intLimit    An optional limit
     * @param int   $intOffset   An optional offset
     * @param array $arrOptions  An optional options array
     *
     * @return Collection<self>|self|null
     */
    public static function findPublishedByPids($arrPids, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = []): Collection|self|null
    {
        if (!\is_array($arrPids) || [] === $arrPids) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = ["$t.pid IN(".implode(',', array_map(intval(...), $arrPids)).')'];

        if (true === $blnFeatured) {
            $arrColumns[] = "$t.featured=1";
        } elseif (false === $blnFeatured) {
            $arrColumns[] = "$t.featured=''";
        }

        // Never return unpublished elements in the back end, so they don't end up in the
        // RSS feed
        if (!static::isPreviewMode($arrOptions) || TL_MODE === 'BE') {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        $arrOptions['order'] ??= "$t.date DESC";

        $arrOptions['limit'] = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Count published product items by their parent ID.
     *
     * @param array $arrPids     An array of product catalogs IDs
     * @param bool  $blnFeatured If true, return only featured product, if false, return only unfeatured product
     * @param array $arrOptions  An optional options array
     *
     * @return int The number of product items
     */
    public static function countPublishedByPids($arrPids, $blnFeatured = null, array $arrOptions = []): int
    {
        if (!\is_array($arrPids) || [] === $arrPids) {
            return 0;
        }

        $t = static::$strTable;
        $arrColumns = ["$t.pid IN(".implode(',', array_map(intval(...), $arrPids)).')'];

        if (true === $blnFeatured) {
            $arrColumns[] = "$t.featured=1";
        } elseif (false === $blnFeatured) {
            $arrColumns[] = "$t.featured=''";
        }

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::countBy($arrColumns, null, $arrOptions);
    }

    /**
     * Find published product items with the default redirect target by their parent ID.
     *
     * @param int   $intPid     The product catalogs ID
     * @param array $arrOptions An optional options array
     *
     * @return Collection<self>|self|null
     */
    public static function findPublishedDefaultByPid($intPid, array $arrOptions = []): Collection|self|null
    {
        $t = static::$strTable;
        $arrColumns = ["$t.pid=?"];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        $arrOptions['order'] ??= "$t.date DESC";

        return static::findBy($arrColumns, $intPid, $arrOptions);
    }

    /**
     * Find published product items by their parent ID.
     *
     * @param int   $intPid      The product catalogs ID
     * @param int   $intLimit   An optional limit
     * @param array $arrOptions An optional options array
     *
     * @return Collection<self>|self|null
     */
    public static function findPublishedByPid($intPid, $intLimit = 0, array $arrOptions = []): Collection|self|null
    {
        $time = time();
        $t = static::$strTable;

        $arrColumns = ["$t.pid=? AND ($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1"];

        $arrOptions['order'] ??= "$t.date DESC";

        if ($intLimit > 0) {
            $arrOptions['limit'] = $intLimit;
        }

        return static::findBy($arrColumns, $intPid, $arrOptions);
    }
}
