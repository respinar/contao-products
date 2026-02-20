<?php

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\System;
use Contao\BackendUser;

/**
 * Add palettes to tl_module
 */
$GLOBALS["TL_DCA"]["tl_module"]["palettes"]["product_list"] = '
	{title_legend},name,headline,type;
	{catalog_legend},product_catalogs,product_featured,product_detailModule,product_sortBy,numberOfItems,perPage,skipFirst;
	{template_legend},customTpl,product_listClass,product_template,product_singleClass;
	{image_legend},imgSize;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';
$GLOBALS["TL_DCA"]["tl_module"]["palettes"]["product_detail"] = '
	{title_legend},name,headline,type;
	{catalog_legend},product_catalogs,overviewPage,customLabel;
	{template_legend},customTpl,product_template;
	{image_legend},imgSize;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';
$GLOBALS["TL_DCA"]["tl_module"]["palettes"]["product_related"] = '
	{title_legend},name,headline,type;
	{template_legend},customTpl,product_listClass,product_template;
	{image_legend},imgSize;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_module
 */
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_catalogs"] = [
  "inputType" => "checkbox",
  "foreignKey" => "tl_product_catalog.title",
  "eval" => ["multiple" => true, "mandatory" => true],
  "sql" => "blob NULL",
];
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_categories"] = [
  "inputType" => "treePicker",
  "foreignKey" => "tl_product_category.title",
  "eval" => [
    "multiple" => true,
    "fieldType" => "checkbox",
    "foreignTable" => "tl_product_category",
    "titleField" => "title",
    "searchField" => "title",
    "managerHref" => "table=tl_product_category",
  ],
  "sql" => "blob NULL",
];
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_featured"] = [
  "default" => "all_product",

  "inputType" => "select",
  "options" => ["all_product", "featured_product", "unfeatured_product"],
  "reference" => &$GLOBALS["TL_LANG"]["tl_module"],
  "eval" => ["tl_class" => "w50"],
  "sql" => "varchar(20) NOT NULL default ''",
];
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_sortBy"] = [
  "default" => "custom",

  "inputType" => "select",
  "options" => ["custom", "date_desc", "date_asc", "title_asc", "title_desc"],
  "reference" => &$GLOBALS["TL_LANG"]["tl_module"],
  "eval" => ["tl_class" => "w50"],
  "sql" => "varchar(16) NOT NULL default ''",
];
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_detailModule"] = [
  "inputType" => "select",
  "reference" => &$GLOBALS["TL_LANG"]["tl_module"],
  "eval" => ["includeBlankOption" => true, "tl_class" => "w50"],
  "sql" => "int(10) unsigned NOT NULL default '0'",
];
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_template"] = [
  "default" => "product_short",

  "inputType" => "select",
  "eval" => ["tl_class" => "w50 clr"],
  "sql" => "varchar(64) NOT NULL default ''",
];
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_listClass"] = [
  "inputType" => "text",
  "eval" => ["maxlength" => 128, "tl_class" => "w50"],
  "sql" => "varchar(255) NOT NULL default ''",
];
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_singleClass"] = [
  "inputType" => "text",
  "eval" => ["maxlength" => 128, "tl_class" => "w50"],
  "sql" => "varchar(255) NOT NULL default ''",
];
$GLOBALS["TL_DCA"]["tl_module"]["fields"]["product_comHeadline"] = [
  "search" => true,
  "inputType" => "inputUnit",
  "options" => ["h1", "h2", "h3", "h4", "h5", "h6"],
  "eval" => ["maxlength" => 200, "tl_class" => "w50 clr"],
  "sql" =>
    "varchar(255) NOT NULL default 'a:2:{s:5:\"value\";s:0:\"\";s:4:\"unit\";s:2:\"h2\";}'",
];

$bundles = System::getContainer()->getParameter("kernel.bundles");

// Add the comments template drop-down menu
if (isset($bundles["ContaoCommentsBundle"])) {
  $GLOBALS["TL_DCA"]["tl_module"]["palettes"]["product_detail"] = str_replace(
    "{protected_legend:hide}",
    "{comment_legend:hide},product_comHeadline,com_template;{protected_legend:hide}",
    $GLOBALS["TL_DCA"]["tl_module"]["palettes"]["product_detail"],
  );
}
