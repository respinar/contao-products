<?php

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\System;
use Contao\DataContainer;
use Contao\Backend;
use Contao\BackendUser;
use Contao\DC_Table;
use Contao\FilesModel;
use Contao\Image;
use Contao\StringUtil;
use Contao\Config;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;

System::loadLanguageFile("tl_content");

/**
 * Table tl_product
 */
$GLOBALS["TL_DCA"]["tl_product"] = [
  // Config
  "config" => [
    "dataContainer" => DC_Table::class,
    "ptable" => "tl_product_catalog",
    "ctable" => ["tl_content"],
    "switchToEdit" => true,
    "enableVersioning" => true,
    "sql" => [
      "keys" => [
        "id" => "primary",
        "alias" => "index",
        "pid,start,stop,published" => "index",
      ],
    ],
  ],

  // List
  "list" => [
    "sorting" => [
      "mode" => DataContainer::MODE_PARENT,
      "fields" => ["sorting"],
      "headerFields" => [
        "title",
        "overviewPage",
        "jumpTo",
        "language",
        "protected",
      ],
      "panelLayout" => "filter;sort,search,limit",
      "child_record_callback" => ["tl_product", "generateProductsRow"],
    ],    
    "operations" => [
      "edit",
      "children",
      "copy",
      "cut",
      "delete",
      "toggle" => [
        "href" => "act=toggle&amp;field=published",
        "icon" => "visible.svg",
        "primary" => true,
        "showInHeader" => true,
      ],
      "feature" => [
        "href" => "act=toggle&amp;field=featured",
        "icon" => "featured.svg",
        "primary" => true,
      ],
      "show",
    ],
  ],

  // Palettes
  "palettes" => [
    "__selector__" => ["addEnclosure", "overwriteMeta"],
    "default" =>
      "{title_legend},title,alias,featured;{meta_legend},pageTitle,date,description;{summary_legend},summary;{offer_legend:hide},price,availability,priceValidUntil;{rating_legend},rating_value,rating_count,visit;{product_legend},brand,model,sku,global_ID;{image_legend},singleSRC,overwriteMeta;{related_legend},related;{link_legend:hide},url,target,titleText,linkTitle;{enclosure_legend:hide},addEnclosure;{expert_legend:hide},cssClass;{publish_legend},published,start,stop",
  ],

  // Subpalettes
  "subpalettes" => [
    "addEnclosure" => "enclosure",
    "overwriteMeta" => "alt,imageTitle",
  ],

  // Fields
  "fields" => [
    "id" => [
      "sql" => array('type'=>'integer', 'unsigned'=>true, 'autoincrement'=>true),
    ],
    "pid" => [
      "foreignKey" => "tl_product_catalog.title",
      "sql" => array('type'=>'integer', 'unsigned'=>true, 'default'=>0),
      "relation" => ["type" => "belongsTo", "load" => "lazy"],
    ],
    "sorting" => [
      "sql" => array('type'=>'integer', 'unsigned'=>true, 'autoincrement'=>true),
    ],
    "tstamp" => [
      "sql" => array('type'=>'integer', 'unsigned'=>true, 'autoincrement'=>true),
    ],
    "visit" => [
      "sorting" => true,
      "inputType" => "text",
      "eval" => ["disabled" => true, "tl_class" => "w50"],
      "sql" => array('type'=>'integer', 'unsigned'=>true, 'autoincrement'=>true),
    ],
    "title" => [
      "search" => true,
      "sorting" => true,
      "inputType" => "text",
      "eval" => ["mandatory" => true, "maxlength" => 128, "tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "alias" => [
      "search" => true,
      "inputType" => "text",
      "eval" => [
        "mandatory" => true,
        "rgxp" => "alias",
        "unique" => true,
        "maxlength" => 128,
        "tl_class" => "w50 clr",
      ],
      "save_callback" => [["tl_product", "generateAlias"]],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>'', 'platformOptions'=>array('collation'=>'utf8mb4_bin')),
    ],
    // 'categories' => array
    // (
    //
    // 	'filter'       => true,
    // 	'inputType'    => 'treePicker',
    // 	'foreignKey'   => 'tl_product_category.title',
    // 	'eval'         => ['multiple'=>true, 'fieldType'=>'checkbox', 'foreignTable'=>'tl_product_category', 'titleField'=>'title', 'searchField'=>'title', 'managerHref'=>'table=tl_product_category'),
    // 	'sql'          => array('type'=>'blob', 'length'=>AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull'=>false)
    // ),
    "brand" => [
      "search" => true,
      "sorting" => true,
      "inputType" => "text",
      "eval" => ["maxlength" => 128, "tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "model" => [
      "search" => true,
      "sorting" => true,
      "inputType" => "text",
      "eval" => ["maxlength" => 128, "tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "global_ID" => [
      "search" => true,
      "sorting" => true,
      "options" => ["mpn", "isbn", "gtin8", "gtin12", "gtin13", "gtin14"],
      "inputType" => "inputUnit",
      "reference" => &$GLOBALS["TL_LANG"]["MSC"],
      "eval" => [
        "includeBlankOption" => true,
        "maxlength" => 128,
        "tl_class" => "w50",
      ],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "sku" => [
      "search" => true,
      "sorting" => true,
      "inputType" => "text",
      "eval" => ["maxlength" => 128, "tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "availability" => [
      "inputType" => "select",
      "options" => [
        "Discontinued",
        "InStock",
        "InStoreOnly",
        "LimitedAvailability",
        "OnlineOnly",
        "OutOfStock",
        "PreOrder",
        "PreSale",
        "SoldOut",
      ],
      "reference" => &$GLOBALS["TL_LANG"]["MSC"],
      "eval" => ["tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "price" => [
      "search" => true,
      "sorting" => true,
      "options" => ["IRR", "TMN", "USD", "EUR"],
      "inputType" => "inputUnit",
      "reference" => &$GLOBALS["TL_LANG"]["MSC"],
      "eval" => [
        "includeBlankOption" => true,
        "maxlength" => 128,
        "tl_class" => "w50",
      ],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "priceValidUntil" => [
      "default" => time(),
      "filter" => true,
      "flag" => 8,
      "inputType" => "text",
      "eval" => [
        "rgxp" => "date",
        "doNotCopy" => true,
        "datepicker" => true,
        "tl_class" => "w50 wizard",
      ],
      "sql" => array('type'=>'integer', 'unsigned'=>true, 'autoincrement'=>true),
    ],
    "rating_value" => [
      "sorting" => true,
      "inputType" => "text",
      "eval" => ["tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>10, 'default'=>''),
    ],
    "rating_count" => [
      "sorting" => true,
      "inputType" => "text",
      "eval" => ["tl_class" => "w50"],
      "sql" => array('type'=>'integer', 'unsigned'=>true),
    ],
    "date" => [
      "default" => time(),

      "filter" => true,
      "flag" => 8,
      "inputType" => "text",
      "eval" => [
        "rgxp" => "date",
        "doNotCopy" => true,
        "datepicker" => true,
        "tl_class" => "w50 wizard",
      ],
      "sql" => array('type'=>'integer', 'unsigned'=>true, 'autoincrement'=>true),
    ],
    "url" => [
      "search" => true,
      "inputType" => "text",
      "eval" => [
        "rgxp" => "url",
        "decodeEntities" => true,
        "maxlength" => 255,
        "dcaPicker" => true,
        "tl_class" => "w50",
      ],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "target" => [
      "inputType" => "checkbox",
      "eval" => ["tl_class" => "w50 m12"],
      "sql" => ["type" => "boolean", "default" => false],
    ],
    "titleText" => [
      "search" => true,
      "inputType" => "text",
      "eval" => ["maxlength" => 255, "tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "linkTitle" => [
      "search" => true,
      "inputType" => "text",
      "eval" => ["maxlength" => 255, "tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "summary" => [
      "search" => true,
      "inputType" => "textarea",
      "eval" => ["rte" => "tinyMCE", "tl_class" => "clr"],
      "sql" => array('type'=>'text', 'length'=>AbstractMySQLPlatform::LENGTH_LIMIT_TEXT, 'notnull'=>false),
    ],
    "pageTitle" => [
      "search" => true,
      "inputType" => "text",
      "eval" => [
        "maxlength" => 255,
        "decodeEntities" => true,
        "tl_class" => "w50",
      ],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "description" => [
      "inputType" => "textarea",
      "search" => true,
      "eval" => [
        "style" => "unicode-bidi: plaintext;",
        "rows" => "2",
        "decodeEntities" => true,
        "tl_class" => "clr",
      ],
      "sql" => array('type'=>'text', 'length'=>AbstractMySQLPlatform::LENGTH_LIMIT_TEXT, 'notnull'=>false),
    ],
    "singleSRC" => [
      "inputType" => "fileTree",
      "eval" => [
        "mandatory" => true,
        "fieldType" => "radio",
        "files" => true,
        "filesOnly" => true,
        "extensions" => "%contao.image.valid_extensions%",
      ],
      "sql" => array('type'=>'binary', 'length'=>16, 'fixed'=>true, 'notnull'=>false),
    ],
    "overwriteMeta" => [
      "label" => &$GLOBALS["TL_LANG"]["tl_content"]["overwriteMeta"],

      "inputType" => "checkbox",
      "eval" => ["submitOnChange" => true, "tl_class" => "w50 clr"],
      "sql" => ["type" => "boolean", "default" => false],
    ],
    "alt" => [
      "label" => &$GLOBALS["TL_LANG"]["tl_content"]["alt"],

      "search" => true,
      "inputType" => "text",
      "eval" => ["maxlength" => 255, "tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "imageTitle" => [
      "label" => &$GLOBALS["TL_LANG"]["tl_content"]["imageTitle"],

      "search" => true,
      "inputType" => "text",
      "eval" => ["maxlength" => 255, "tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "addEnclosure" => [
      "inputType" => "checkbox",
      "eval" => ["submitOnChange" => true],
      "sql" => ["type" => "boolean", "default" => false],
    ],
    "enclosure" => [
      "inputType" => "fileTree",
      "eval" => [
        "multiple" => true,
        "fieldType" => "checkbox",
        "filesOnly" => true,
        "isDownloads" => true,
        "extensions" => Config::get("allowedDownload"),
        "mandatory" => true,
      ],
      "sql" => array('type'=>'blob', 'length'=>AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull'=>false),
    ],
    "related" => [
      "exclude" => false,
      "inputType" => "checkbox",
      "options_callback" => ["tl_product", "getProducts"],
      "eval" => ["includeBlankOption" => true, "multiple" => true],
      "sql" => array('type'=>'blob', 'length'=>AbstractMySQLPlatform::LENGTH_LIMIT_BLOB, 'notnull'=>false),
    ],
    "cssClass" => [
      "inputType" => "text",
      "eval" => ["tl_class" => "w50"],
      "sql" => array('type'=>'string', 'length'=>255, 'default'=>''),
    ],
    "published" => [
      "toggle" => true,
      "filter" => true,
      "flag" => 1,
      "inputType" => "checkbox",
      "eval" => ["doNotCopy" => true],
      "sql" => ["type" => "boolean", "default" => false],
    ],
    "featured" => [
      "toggle" => true,
      "filter" => true,
      "inputType" => "checkbox",
      "eval" => ["tl_class" => "w50 m12"],
      "sql" => ["type" => "boolean", "default" => false],
    ],
    "start" => [
      "inputType" => "text",
      "eval" => [
        "rgxp" => "datim",
        "datepicker" => true,
        "tl_class" => "w50 wizard",
      ],
      "sql" => array('type'=>'string', 'length'=>10, 'default'=>''),
    ],
    "stop" => [
      "inputType" => "text",
      "eval" => [
        "rgxp" => "datim",
        "datepicker" => true,
        "tl_class" => "w50 wizard",
      ],
      "sql" => array('type'=>'string', 'length'=>10, 'default'=>''),
    ],
  ],
];

/**
 * Provide miscellaneous methods that are used by the data configuration array
 */
class tl_product extends Backend
{
  /**
   * Import the back end user object
   */
  public function __construct()
  {
    parent::__construct();
    $this->import(BackendUser::class, "User");
  }

  /**
   * Auto-generate the product alias if it has not been set yet
   */
  public function generateAlias(string $varValue, DataContainer $dc): string
  {
    $autoAlias = false;

    // Generate alias if there is none
    if ($varValue === "") {
      $autoAlias = true;
      $varValue = StringUtil::standardize(
        String::restoreBasicEntities($dc->activeRecord->title),
      );
    }

    $objAlias = $this->Database
      ->prepare("SELECT id FROM tl_product WHERE alias=?")
      ->execute($varValue);

    // Check whether the product alias exists
    if ($objAlias->numRows > 1 && !$autoAlias) {
      throw new Exception(
        sprintf($GLOBALS["TL_LANG"]["ERR"]["aliasExists"], $varValue),
      );
    }

    // Add ID to alias
    if ($objAlias->numRows && $autoAlias) {
      $varValue .= "-" . $dc->id;
    }

    return $varValue;
  }

  /**
   * Generate a song row and return it as HTML string
   */
  public function generateProductsRow(array $arrRow): string
  {
    // $objImage = FilesModel::findByPk($arrRow['singleSRC']);

    // if ($objImage !== null)
    // {
    // 	$strImage = Image::getHtml(Image::get($objImage->path, '60', '60', 'center_center'));
    // }

    // return '<div><div style="float:left; margin-right:10px;">'.$strImage.'</div><p><strong>'. $arrRow['title'].'</strong></p><p> Brand: '.$arrRow['brand'] .' &emsp; Model: '. $arrRow['model']. ' &emsp; SKU: '. $arrRow['sku'] . ' &emsp; Visit: '. $arrRow['visit'] .'</p></div>';

    return "<div><p><strong>" .
      $arrRow["title"] .
      "</strong></p><p> Brand: " .
      $arrRow["brand"] .
      " &emsp; Model: " .
      $arrRow["model"] .
      " &emsp; SKU: " .
      $arrRow["sku"] .
      " &emsp; Visit: " .
      $arrRow["visit"] .
      "</p></div>";
  }

  /**
   * Get records from the master category
   */
  public function getProducts(DataContainer $dc): array
  {
    $arrItems = [];

    $objItems = $this->Database
      ->prepare("SELECT * FROM tl_product WHERE pid=? ORDER BY date DESC")
      ->execute($dc->activeRecord->pid);

    while ($objItems->next()) {
      if ($objItems->id !== $dc->activeRecord->id) {
        $arrItems[$objItems->id] = $objItems->title;

        if ($objItems->model) {
          $arrItems[$objItems->id] .= " [model: " . $objItems->model . "]";
        }

        if ($objItems->sku) {
          $arrItems[$objItems->id] .= " (sku: " . $objItems->sku . ")";
        }
      }
    }

    return $arrItems;
  }

  /**
   * Update the category relations
   * @param DataContainer
   */
  // public function updateCategories(DataContainer $dc)
  // {
  //     $this->import('BackendUser', 'User');
  //     $arrCategories = StringUtil::deserialize($dc->activeRecord->categories);

  //     // Use the default categories if the user is not allowed to edit the field directly
  //     if (!$this->User->isAdmin && !in_['tl_product::categories', $this->User->alexf)) {

  //         // Return if the record is not new
  //         if ($dc->activeRecord->tstamp) {
  //             return;
  //         }

  //         $arrCategories = $this->User->productcategories_default;
  //     }

  //     $this->deleteCategories($dc);

  //     if (is_[$arrCategories) && !empty($arrCategories)) {
  //         foreach ($arrCategories as $intCategory) {
  //             $this->Database->prepare("INSERT INTO tl_product_categories (category_id, product_id) VALUES (?, ?)")
  //                            ->execute($intCategory, $dc->id);
  //         }

  //         $this->Database->prepare("UPDATE tl_product SET categories=? WHERE id=?")
  //                        ->execute(serialize($arrCategories), $dc->id);
  //     }
  // }

  /**
   * Delete the category relations
   * @param DataContainer
   */
  // public function deleteCategories(DataContainer $dc)
  // {
  //     $this->Database->prepare("DELETE FROM tl_product_categories WHERE product_id=?")
  //                    ->execute($dc->id);
  // }
}
