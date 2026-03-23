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
 * Extend the default palette
 */
PaletteManipulator::create()
    ->addLegend('product_legend', 'amg_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField('products', 'product_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group')
;

/*
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['products'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user']['products'],
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_product_catalog.title',
    'eval' => ['multiple' => true],
    'sql' => ['type' => 'blob', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false],
    'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
];
