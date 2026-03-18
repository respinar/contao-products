<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;

/*
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace(
    'formp;',
    'formp;{product_legend},products,productp;',
    $GLOBALS['TL_DCA']['tl_user']['palettes']['extend'],
);
$GLOBALS['TL_DCA']['tl_user']['palettes']['custom'] = str_replace(
    'formp;',
    'formp;{product_legend},products,productp;',
    $GLOBALS['TL_DCA']['tl_user']['palettes']['custom'],
);

/*
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['products'] = [
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_product_catalog.title',
    'eval' => ['multiple' => true],
    'sql' => ['type' => 'blob', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_user']['fields']['productp'] = [
    'inputType' => 'checkbox',
    'options' => ['create', 'delete'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['multiple' => true],
    'sql' => ['type' => 'blob', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false],
];
