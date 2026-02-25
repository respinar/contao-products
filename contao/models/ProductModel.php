<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Model;

use Contao\CoreBundle\File\ModelMetadataTrait;
use Contao\Model;
use Model\Collection;

class ProductModel extends Model
{
    use ModelMetadataTrait;

    protected static $strTable = 'tl_product';

    /**
     * Find published product items by their parent ID and ID or alias.
     *
     * @param mixed $varId      The numeric ID or alias name
     * @param array $arrOptions An optional options array
     *
     * @return \Model|null The productModel or null if there are no product
     */
    public static function findPublishedByIdOrAlias($varId, array $arrOptions = [])
    {
        $t = static::$strTable;
        $arrColumns = ["($t.id=? OR $t.alias=?)"];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findBy($arrColumns, [is_numeric($varId) ? $varId : 0, $varId], $arrOptions);
    }

    /**
     * Find published product items by their parent ID and ID or alias.
     *
     * @param mixed $varId      The numeric ID or alias name
     * @param array $arrPids    An array of parent IDs
     * @param array $arrOptions An optional options array
     *
     * @return \Model|null The productModel or null if there are no product
     */
    public static function findPublishedByParentAndIdOrAlias($varId, $arrPids, array $arrOptions = [])
    {
        if (!\is_array($arrPids) || [] === $arrPids) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = ["($t.id=? OR $t.alias=?) AND $t.pid IN(".implode(',', array_map('intval', $arrPids)).')'];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findBy($arrColumns, [is_numeric($varId) ? $varId : 0, $varId], $arrOptions);
    }

    /**
     * Find published product items by their parent ID.
     *
     * @param bool  $blnFeatured If true, return only featured product, if false, return only unfeatured product
     * @param int   $intLimit    An optional limit
     * @param int   $intOffset   An optional offset
     * @param array $arrOptions  An optional options array
     *
     * @return Collection|null A collection of models or null if there are no product
     */
    public static function findPublishedByIds($arrIds, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!\is_array($arrIds) || [] === $arrIds) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = ["$t.id IN(".implode(',', array_map('intval', $arrIds)).')'];

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

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.date DESC";
        }

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
     * @return Collection|null A collection of models or null if there are no product
     */
    public static function findPublishedByPids($arrPids, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!\is_array($arrPids) || [] === $arrPids) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = ["$t.pid IN(".implode(',', array_map('intval', $arrPids)).')'];

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

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.date DESC";
        }

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
    public static function countPublishedByPids($arrPids, $blnFeatured = null, array $arrOptions = [])
    {
        if (!\is_array($arrPids) || [] === $arrPids) {
            return 0;
        }

        $t = static::$strTable;
        $arrColumns = ["$t.pid IN(".implode(',', array_map('intval', $arrPids)).')'];

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
     * @return Collection|null A collection of models or null if there are no product
     */
    public static function findPublishedDefaultByPid($intPid, array $arrOptions = [])
    {
        $t = static::$strTable;
        $arrColumns = ["$t.pid=?"];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.date DESC";
        }

        return static::findBy($arrColumns, $intPid, $arrOptions);
    }

    /**
     * Find published product items by their parent ID.
     *
     * @param int   $intId      The product catalogs ID
     * @param int   $intLimit   An optional limit
     * @param array $arrOptions An optional options array
     *
     * @return Collection|null A collection of models or null if there are no product
     */
    public static function findPublishedByPid($intId, $intLimit = 0, array $arrOptions = [])
    {
        $time = time();
        $t = static::$strTable;

        $arrColumns = ["$t.pid=? AND ($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1"];

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.date DESC";
        }

        if ($intLimit > 0) {
            $arrOptions['limit'] = $intLimit;
        }

        return static::findBy($arrColumns, $intId, $arrOptions);
    }

    /**
     * Find all published product items of a certain period of time by their parent ID.
     *
     * @param int   $intFrom    The start date as Unix timestamp
     * @param int   $intTo      The end date as Unix timestamp
     * @param array $arrPids    An array of product catalogs IDs
     * @param int   $intLimit   An optional limit
     * @param int   $intOffset  An optional offset
     * @param array $arrOptions An optional options array
     *
     * @return Collection|null A collection of models or null if there are no product
     */
    public static function findPublishedFromToByPids($intFrom, $intTo, $arrPids, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!\is_array($arrPids) || [] === $arrPids) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = ["$t.date>=? AND $t.date<=? AND $t.pid IN(".implode(',', array_map('intval', $arrPids)).')'];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit'] = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, [$intFrom, $intTo], $arrOptions);
    }

    /**
     * Count all published product items of a certain period of time by their parent ID.
     *
     * @param int   $intFrom    The start date as Unix timestamp
     * @param int   $intTo      The end date as Unix timestamp
     * @param array $arrPids    An array of product catalogs IDs
     * @param array $arrOptions An optional options array
     *
     * @return int The number of product items
     */
    public static function countPublishedFromToByPids($intFrom, $intTo, $arrPids, array $arrOptions = [])
    {
        if (!\is_array($arrPids) || [] === $arrPids) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = ["$t.date>=? AND $t.date<=? AND $t.pid IN(".implode(',', array_map('intval', $arrPids)).')'];

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::countBy($arrColumns, [$intFrom, $intTo], $arrOptions);
    }
}
