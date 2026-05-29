<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        'onload_callback' => [
            ['tl_product', 'checkPermission'],
        ],
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
            'options_callback' => ['tl_product', 'getLanguageMainOptions'],
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
            'save_callback' => [['tl_product', 'generateAlias']],
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
            'options_callback' => ['tl_product', 'getProducts'],
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

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class tl_product extends Backend
{
    /**
     * Auto-generate the product alias if it has not been set yet.
     *
     * The alias only has to be unique within the products that resolve to the same
     * reader page (jumpTo), so the same product may use the same alias in another
     * catalog/language with a different reader page.
     */
    public function generateAlias(string $varValue, DataContainer $dc): string
    {
        $autoAlias = false;

        // Generate alias if there is none
        if ('' === $varValue) {
            $autoAlias = true;
            $varValue = StringUtil::standardize(
                StringUtil::restoreBasicEntities($dc->activeRecord->title),
            );
        }

        $connection = System::getContainer()->get('database_connection');

        // The alias must be unique among all products whose catalog points to the same
        // reader page (jumpTo) as the current product.
        $jumpTo = (int) $connection->fetchOne(
            'SELECT jumpTo FROM tl_product_catalog WHERE id = ?',
            [$dc->activeRecord->pid],
        );

        $aliasExists = static fn (string $alias): bool => 0 < (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM tl_product WHERE alias = ? AND id != ? AND pid IN (SELECT id FROM tl_product_catalog WHERE jumpTo = ?)',
            [$alias, $dc->id, $jumpTo],
        );

        if ($autoAlias) {
            // Make sure the generated alias is unique within the same reader page
            $base = $varValue;
            $i = 0;

            while ($aliasExists($varValue)) {
                $varValue = $base.'-'.++$i;
            }
        } elseif ($aliasExists($varValue)) {
            throw new RuntimeException(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
    }

    /**
     * Generate a product row and return it as HTML string.
     *
     * @param array $arrRow - the product row data as an associative array
     */
    public function generateProductsRow(array $arrRow): string
    {
        // $objImage = FilesModel::findByPk($arrRow['singleSRC']); if ($objImage !==
        // null) { 	$strImage = Image::getHtml(Image::get($objImage->path, '60', '60',
        // 'center_center')); } return '<div><div style="float:left;
        // margin-right:10px;">'.$strImage.'</div><p><strong>'.
        // $arrRow['title'].'</strong></p><p> Brand: '.$arrRow['brand'] .' &emsp; Model:
        // '. $arrRow['model']. ' &emsp; SKU: '. $arrRow['sku'] . ' &emsp; Visit: '.
        // $arrRow['visit'] .'</p></div>';

        return '<div><p><strong>'.
          $arrRow['title'].
          '</strong></p><p> Brand: '.
          $arrRow['brand'].
          ' &emsp; Model: '.
          $arrRow['model'].
          ' &emsp; SKU: '.
          $arrRow['sku'].
          ' &emsp; Visit: '.
          $arrRow['visit'].
          '</p></div>';
    }

    /**
     * Get records from the master category.
     */
    public function getProducts(DataContainer $dc): array
    {
        $arrItems = [];

        $connection = System::getContainer()->get('database_connection');
        $rows = $connection->fetchAllAssociative('SELECT * FROM tl_product WHERE pid = ? ORDER BY date DESC', [$dc->activeRecord->pid]);

        foreach ($rows as $objItems) {
            if ($objItems['id'] !== $dc->activeRecord->id) {
                $arrItems[$objItems['id']] = $objItems['title'];

                if ($objItems['model']) {
                    $arrItems[$objItems['id']] .= ' [model: '.$objItems['model'].']';
                }

                if ($objItems['sku']) {
                    $arrItems[$objItems['id']] .= ' (sku: '.$objItems['sku'].')';
                }
            }
        }

        return $arrItems;
    }

    /**
     * Get products from the master catalog to link as language main.
     *
     * Only offers master products that have not already been assigned to another
     * product in the same catalog, so each master product can only be translated once
     * per language.
     */
    public function getLanguageMainOptions(DataContainer $dc): array
    {
        if (null === $dc->activeRecord) {
            return [];
        }

        $connection = System::getContainer()->get('database_connection');

        $catalog = $connection->fetchAssociative('SELECT * FROM tl_product_catalog WHERE id = ?', [$dc->activeRecord->pid]);

        if (false === $catalog) {
            return [];
        }

        // Use the master catalog (or the current catalog if it has none)
        $intMaster = (int) $catalog['master'] ?: (int) $catalog['id'];
        $intCurrent = (int) $dc->activeRecord->id;

        // Exclude master products that are already used by another product in this catalog
        $usedIds = $connection->fetchFirstColumn(
            'SELECT languageMain FROM tl_product WHERE pid = ? AND id != ? AND languageMain != 0',
            [$dc->activeRecord->pid, $intCurrent],
        );

        $arrOptions = [];
        $rows = $connection->fetchAllAssociative('SELECT id, title, model, sku FROM tl_product WHERE pid = ? ORDER BY title', [$intMaster]);

        foreach ($rows as $objItems) {
            $id = (int) $objItems['id'];

            if ($id === $intCurrent || in_array($id, array_map('intval', $usedIds), true)) {
                continue;
            }

            $label = $objItems['title'];

            if ($objItems['model']) {
                $label .= ' [model: '.$objItems['model'].']';
            }

            if ($objItems['sku']) {
                $label .= ' (sku: '.$objItems['sku'].')';
            }

            $arrOptions[$id] = $label;
        }

        return $arrOptions;
    }

    public function checkPermission(): void
    {
        $u = BackendUser::getInstance();
        if ($u->isAdmin) {
            return;
        }
        $u->products = is_array($u->products) && $u->products ? $u->products : [0];
        $a = Input::get('act');
        $i = Input::get('id');

        switch ($a) {
            case 'create':
                if (!in_array(Input::get('pid') ?? $i, $u->products, true)) {
                    throw new AccessDeniedException('Not enough permissions to create products in this catalog.');
                }
                break;
            case 'edit': case 'copy': case 'cut': case 'delete': case 'show': case 'toggle':
                $r = System::getContainer()->get('database_connection')->fetchAssociative('SELECT pid FROM tl_product WHERE id=?', [$i]);
                if (!$r || !in_array($r['pid'], $u->products, true)) {
                    throw new AccessDeniedException('Not enough permissions to '.$a.' product ID '.$i.'.');
                }
                break;
            case 'paste':
                if (!in_array(Input::get('pid'), $u->products, true)) {
                    throw new AccessDeniedException('Not enough permissions to paste products into this catalog.');
                }
                break;
        }
    }
}
