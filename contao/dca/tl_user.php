<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;

/*
 * Extend the default palettes
 */
PaletteManipulator::create()
    ->addLegend('product_legend', 'amg_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField('products', 'product_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('extend', 'tl_user')
    ->applyToPalette('custom', 'tl_user')
;

/*
 * Add fields to tl_user
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['products'] = [
    'inputType'  => 'checkbox',
    'foreignKey' => 'tl_product_catalog.title',
    'eval'       => ['multiple' => true],
    'sql'        => ['type' => 'blob', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false],
    'relation'   => ['type' => 'hasMany', 'load' => 'lazy'],
];
