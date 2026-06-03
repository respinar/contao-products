<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

use Contao\Config;
use Contao\DataContainer;
use Contao\DC_Table;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;

/*
 * Table tl_product
 */
$GLOBALS['TL_DCA']['tl_product'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_product_catalog',
        'ctable' => ['tl_content'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,alias' => 'unique',
                'pid,start,stop,published' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['sorting'],
            'headerFields' => [
                'title',
                'overviewPage',
                'jumpTo',
                'language',
                'protected',
            ],
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'operations' => [
            'edit',
            'children',
            'copy',
            'cut',
            'delete',
            'toggle' => [
                'href' => 'act=toggle&amp;field=published',
                'icon' => 'visible.svg',
                'primary' => true,
                'showInHeader' => true,
            ],
            'feature' => [
                'href' => 'act=toggle&amp;field=featured',
                'icon' => 'featured.svg',
                'primary' => true,
            ],
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['addEnclosure', 'overwriteMeta'],
        'default' => '{title_legend},title,alias,featured;{language_legend},languageMain;{meta_legend},pageTitle,date,description;{summary_legend},summary;{offer_legend:hide},price,availability,priceValidUntil;{rating_legend},rating_value,rating_count,visit;{product_legend},brand,model,sku,global_ID;{image_legend},singleSRC,overwriteMeta;{related_legend},related;{link_legend:hide},url,target,titleText,linkTitle;{enclosure_legend:hide},addEnclosure;{expert_legend:hide},cssClass;{publish_legend},published,start,stop',
    ],

    // Subpalettes
    'subpalettes' => [
        'addEnclosure' => 'enclosure',
        'overwriteMeta' => 'alt,imageTitle',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'pid' => [
            'foreignKey' => 'tl_product_catalog.title',
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'languageMain' => [
            'exclude' => true,
            'inputType' => 'select',
            'eval' => [
                'includeBlankOption' => true,
                'blankOptionLabel' => &$GLOBALS['TL_LANG']['tl_product']['languageMain'][2],
                'chosen' => true,
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'hasOne', 'table' => 'tl_product', 'field' => 'id', 'load' => 'lazy'],
        ],
        'sorting' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'visit' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['disabled' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'title' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'alias' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'alias',
                'maxlength' => 128,
                'tl_class' => 'w50 clr',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'platformOptions' => ['collation' => 'utf8mb4_bin']],
        ],

        'brand' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'model' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'global_ID' => [
            'search' => true,
            'sorting' => true,
            'options' => ['mpn', 'isbn', 'gtin8', 'gtin12', 'gtin13', 'gtin14'],
            'inputType' => 'inputUnit',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => [
                'includeBlankOption' => true,
                'maxlength' => 128,
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'sku' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'availability' => [
            'inputType' => 'select',
            'options' => [
                'Discontinued',
                'InStock',
                'InStoreOnly',
                'LimitedAvailability',
                'OnlineOnly',
                'OutOfStock',
                'PreOrder',
                'PreSale',
                'SoldOut',
            ],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'price' => [
            'search' => true,
            'sorting' => true,
            'options' => ['IRR', 'TMN', 'USD', 'EUR'],
            'inputType' => 'inputUnit',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => [
                'includeBlankOption' => true,
                'maxlength' => 128,
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'priceValidUntil' => [
            'default' => time(),
            'filter' => true,
            'flag' => 8,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'date',
                'doNotCopy' => true,
                'datepicker' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'rating_value' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => ''],
        ],
        'rating_count' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'date' => [
            'default' => time(),
            'filter' => true,
            'flag' => 8,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'date',
                'doNotCopy' => true,
                'datepicker' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'url' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'url',
                'decodeEntities' => true,
                'maxlength' => 255,
                'dcaPicker' => true,
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'target' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'titleText' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'linkTitle' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'summary' => [
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql' => ['type' => 'text', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_TEXT, 'notnull' => false],
        ],
        'pageTitle' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 255,
                'decodeEntities' => true,
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'description' => [
            'inputType' => 'textarea',
            'search' => true,
            'eval' => [
                'style' => 'unicode-bidi: plaintext;',
                'rows' => '2',
                'decodeEntities' => true,
                'tl_class' => 'clr',
            ],
            'sql' => ['type' => 'text', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_TEXT, 'notnull' => false],
        ],
        'singleSRC' => [
            'inputType' => 'fileTree',
            'eval' => [
                'mandatory' => true,
                'fieldType' => 'radio',
                'files' => true,
                'filesOnly' => true,
                'extensions' => '%contao.image.valid_extensions%',
            ],
            'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
        ],
        'overwriteMeta' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['overwriteMeta'],

            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'alt' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'],

            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'imageTitle' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['imageTitle'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'addEnclosure' => [
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'enclosure' => [
            'inputType' => 'fileTree',
            'eval' => [
                'multiple' => true,
                'fieldType' => 'checkbox',
                'filesOnly' => true,
                'isDownloads' => true,
                'extensions' => Config::get('allowedDownload'),
                'mandatory' => true,
            ],
            'sql' => ['type' => 'blob', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false],
        ],
        'related' => [
            'exclude' => false,
            'inputType' => 'checkbox',
            'eval' => ['includeBlankOption' => true, 'multiple' => true],
            'sql' => ['type' => 'blob', 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false],
        ],
        'cssClass' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'published' => [
            'toggle' => true,
            'filter' => true,
            'flag' => 1,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'featured' => [
            'toggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'start' => [
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'datim',
                'datepicker' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => ''],
        ],
        'stop' => [
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'datim',
                'datepicker' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => ''],
        ],
    ],
];
