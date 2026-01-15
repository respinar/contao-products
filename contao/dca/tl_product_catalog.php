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
		],
		'global_operations' => [
			'all' => [
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			]
		],
		'operations' => [
			'edit',
			'children' ,
			'copy',
			'delete',
			'show'
		]
	],

	// Palettes
	'palettes' => [
		'__selector__'   => ['protected', 'allowComments'],
		'default'        => '{title_legend},title;{redirect_legend},overviewPage,jumpTo;{protected_legend:hide},protected;{comments_legend:hide},allowComments'
	],

	// Subpalettes
	'subpalettes' => [
		'protected'      => 'groups',
		'allowComments'  => 'notify,sortOrder,perPage,moderate,bbcode,requireLogin,disableCaptcha'
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
			'exclude'    => true,
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
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'eval'       => ['submitOnChange'=>true],
			'sql'        => ['type' => 'boolean', 'default' => false]
		],
		'groups' => [
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'foreignKey' => 'tl_member_group.name',
			'eval'       => ['mandatory'=>true, 'multiple'=>true],
			'sql'        => "blob NULL",
			'relation'   => ['type'=>'hasMany', 'load'=>'lazy']
		],
		'allowComments' => [
			'exclude'    => true,
			'filter'     => true,
			'inputType'  => 'checkbox',
			'eval'       => ['submitOnChange'=>true],
			'sql'        => ['type' => 'boolean', 'default' => false]
		],
		'notify' => [
			'default'    => 'notify_admin',
			'exclude'    => true,
			'inputType'  => 'select',
			'options'    => ['notify_admin', 'notify_author', 'notify_both'],
			'eval'       => ['tl_class'=>'w50'],
			'reference'  => &$GLOBALS['TL_LANG']['tl_product_catalog'],
			'sql'        => "varchar(16) NOT NULL default ''"
		],
		'sortOrder' => [
			'default'    => 'ascending',
			'exclude'    => true,
			'inputType'  => 'select',
			'options'    => ['ascending', 'descending'],
			'reference'  => &$GLOBALS['TL_LANG']['MSC'],
			'eval'       => ['tl_class'=>'w50 clr'],
			'sql'        => "varchar(32) NOT NULL default ''"
		],
		'perPage' => [
			'exclude'    => true,
			'inputType'  => 'text',
			'eval'       => ['rgxp'=>'natural', 'tl_class'=>'w50'],
			'sql'        => "smallint(5) unsigned NOT NULL default 0"
		],
		'moderate' => [
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'eval'       => ['tl_class'=>'w50'],
			'sql'        => ['type' => 'boolean', 'default' => false]
		],
		'bbcode' => [
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'eval'       => ['tl_class'=>'w50'],
			'sql'        => ['type' => 'boolean', 'default' => false]
		],
		'requireLogin' => [
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'eval'       => ['tl_class'=>'w50'],
			'sql'        => ['type' => 'boolean', 'default' => false]
		],
		'disableCaptcha' => [
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'eval'       => ['tl_class'=>'w50'],
			'sql'        => ['type' => 'boolean', 'default' => false]
		]
	]
];


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

	/**
     * Return the edit header button
     */
  public function editHeader(array $row, string $href, string $label, string $title, string $icon, string $attributes): void
	{
		// return $this->User->canEditFieldsOf('tl_product_catalog') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}


	/**
     * Return the copy category button
     */
  public function copyCategory(array $row, string $href, string $label, string $title, string $icon, string $attributes): void
	{
		// return $this->User->hasAccess('create', 'catalogp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}


	/**
     * Return the delete category button
     */
  public function deleteCategory(array $row, string $href, string $label, string $title, string $icon, string $attributes): void
	{
		// return $this->User->hasAccess('delete', 'catalogp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}
}
