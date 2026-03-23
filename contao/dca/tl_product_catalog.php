<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\DC_Table;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Respinar\ProductsBundle\Dca\CommentFields;

/*
 * Table tl_product_catalog
 */
$GLOBALS['TL_DCA']['tl_product_catalog'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_product'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'userRoot' => 'products',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'flag' => 1,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['protected'],
        'default' => '{title_legend},title;{redirect_legend},overviewPage,jumpTo;{protected_legend:hide},protected;',
    ],

    // Subpalettes
    'subpalettes' => [
        'protected' => 'groups',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'title' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 128],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'overviewPage' => [
            'inputType' => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval' => ['mandatory' => true, 'fieldType' => 'radio'],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'jumpTo' => [
            'inputType' => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval' => ['mandatory' => true, 'fieldType' => 'radio'],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'protected' => [
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'groups' => [
            'inputType' => 'checkbox',
            'foreignKey' => 'tl_member_group.name',
            'eval' => ['mandatory' => true, 'multiple' => true],
            'sql' => ['type' => 'blob', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false],
            'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
        ],
    ],
];

CommentFields::addTo('tl_product_catalog');
