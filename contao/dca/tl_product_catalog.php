<?php

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\Backend;
use Contao\BackendUser;
use Contao\DC_Table;
use Contao\Image;

use Respinar\ProductsBundle\Dca\CommentFields;

/**
 * Table tl_product_catalog
 */
$GLOBALS['TL_DCA']['tl_product_catalog'] = [

	// Config
	'config' => [
		'dataContainer'    => DC_Table::class,
		'ctable'           => ['tl_product'],
		'enableVersioning' => true,
		'onload_callback'  => [
			['tl_product_catalog', 'checkPermission']
		],
		'sql' => [
			'keys' => [
				'id'    => 'primary'
			]
		]
	],

	// List
	'list' => [
		'sorting' => [
			'mode'        => 1,
			'fields'      => ['title'],
			'flag'        => 1,
			'panelLayout' => 'filter;search,limit'
		],
		'label' => [
			'fields'     => ['title'],
			'format'     => '%s'
		]
	],

	// Palettes
	'palettes' => [
		'__selector__'   => ['protected'],
		'default'        => '{title_legend},title;{redirect_legend},overviewPage,jumpTo;{protected_legend:hide},protected;'
	],

	// Subpalettes
	'subpalettes' => [
		'protected'      => 'groups',
	],

	// Fields
	'fields' => [
		'id' => [
			'sql'        => "int(10) unsigned NOT NULL auto_increment"
		],
		'tstamp' => [
			'sql'        => "int(10) unsigned NOT NULL default 0"
		],
		'title' => [
			'search'     => true,
			'inputType'  => 'text',
			'eval'       => ['mandatory'=>true, 'maxlength'=>128],
			'sql'        => "varchar(255) NOT NULL default ''"
		],
		'overviewPage' => [

			'inputType'  => 'pageTree',
			'foreignKey' => 'tl_page.title',
			'eval'       => ['mandatory'=>true, 'fieldType'=>'radio'],
			'sql'        => "int(10) unsigned NOT NULL default 0",
			'relation'   => ['type'=>'hasOne', 'load'=>'lazy']
		],
		'jumpTo' => [

			'inputType'  => 'pageTree',
			'foreignKey' => 'tl_page.title',
			'eval'       => ['mandatory'=>true, 'fieldType'=>'radio'],
			'sql'        => "int(10) unsigned NOT NULL default 0",
			'relation'   => ['type'=>'hasOne', 'load'=>'lazy']
		],
		'protected' => [
			'inputType'  => 'checkbox',
			'eval'       => ['submitOnChange'=>true],
			'sql'        => ['type' => 'boolean', 'default' => false]
		],
		'groups' => [
			'inputType'  => 'checkbox',
			'foreignKey' => 'tl_member_group.name',
			'eval'       => ['mandatory'=>true, 'multiple'=>true],
			'sql'        => "blob NULL",
			'relation'   => ['type'=>'hasMany', 'load'=>'lazy']
		],
	]
];

CommentFields::addTo('tl_product_catalog');

class tl_product_catalog extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import(BackendUser::class, 'User');
	}

	/**
	* Check permissions to edit table tl_product_catalog
	*/
	public function checkPermission(): void
  {
  }
}
