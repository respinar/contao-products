<?php

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2023 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\System;
use Contao\DataContainer;
use Contao\Backend;
use Contao\FilesModel;
use Contao\Image;
use Contao\StringUtil;

System::loadLanguageFile('tl_content');


/**
 * Table tl_product
 */
$GLOBALS['TL_DCA']['tl_product'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_product_catalog',
		'ctable'                      => array('tl_content'),
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'alias' => 'index',
				'pid,start,stop,published' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => DataContainer::MODE_PARENT,
			'fields'                  => array('sorting'),
			'headerFields'            => array('title','jumpTo','language','protected'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_product', 'generateProductsRow')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'href'                => 'table=tl_content',
				'icon'                => 'edit.svg'
			),
			'editheader' => array
			(
				'href'                => 'act=edit',
				'icon'                => 'header.svg'
			),
			'copy' => array
			(
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.svg'
			),
			'cut' => array
			(
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg'
			),
			'delete' => array
			(
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
			),
			'feature' => array
			(
				'href'                => 'act=toggle&amp;field=featured',
				'icon'                => 'featured.svg',
			),
			'show' => array
			(
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('addEnclosure','overwriteMeta'),
		'default'                     => '{title_legend},title,alias,featured;{meta_legend},pageTitle,date,description;{summary_legend},summary;{offer_legend:hide},price,availability,priceValidUntil;{rating_legend},rating_value,rating_count,visit;{product_legend},brand,model,sku,global_ID;{image_legend},singleSRC,overwriteMeta;{related_legend},related;{link_legend:hide},url,target,titleText,linkTitle;{enclosure_legend:hide},addEnclosure;{publish_legend},published,start,stop',
	),

	// Subpalettes
	'subpalettes' => array
	(
		'addEnclosure'                => 'enclosure',
		'overwriteMeta'               => 'alt,imageTitle'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_product_catalog.title',
			'sql'                     => "int(10) unsigned NOT NULL default 0",
			'relation'                => array('type'=>'belongsTo', 'load'=>'lazy')
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'visit' => array
		(
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('disabled'=>true, 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'title' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>128,'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'alias','unique'=>true,'maxlength'=>128, 'tl_class'=>'w50 clr'),
			'save_callback' => array
			(
				array('tl_product', 'generateAlias')
			),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		// 'categories' => array
		// (
		// 	'exclude'                 => true,
		// 	'filter'                  => true,
		// 	'inputType'               => 'treePicker',
		// 	'foreignKey'              => 'tl_product_category.title',
		// 	'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'foreignTable'=>'tl_product_category', 'titleField'=>'title', 'searchField'=>'title', 'managerHref'=>'table=tl_product_category'),
		// 	'sql'                     => "blob NULL"
		// ),
		'brand' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'model' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'global_ID' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'options'				  => array('mpn','isbn','gtin8','gtin12','gtin13','gtin14'),
			'inputType'               => 'inputUnit',
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('includeBlankOption'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'sku' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'availability' => array
		(
			'inputType'               => 'select',
			'options'                 => array('Discontinued','InStock','InStoreOnly','LimitedAvailability','OnlineOnly','OutOfStock','PreOrder','PreSale','SoldOut'),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'price' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'options'				  => array('IRR','TMN','USD','EUR'),
			'inputType'               => 'inputUnit',
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('includeBlankOption'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'priceValidUntil' => array
		(
			'default'                 => time(),
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'rating_value' => array
		(
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(10) NOT NULL default 0"
		),
		'rating_count' => array
		(
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "int(10) NOT NULL default 0"
		),
		'date' => array
		(
			'default'                 => time(),
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'url' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'target' => array
		(
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => array('type' => 'boolean', 'default' => false)
		),
		'titleText' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'linkTitle' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'summary' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('rte'=>'tinyMCE', 'tl_class'=>'clr'),
			'sql'                     => "text NULL"
		),
		'pageTitle' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'decodeEntities'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'description' => array
		(
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'search'                  => true,
			'eval'                    => array('style'=>'unicode-bidi: plaintext;', 'rows'=>'2', 'decodeEntities'=>true, 'tl_class'=>'clr'),
			'sql'                     => "text NULL"
		),
		'singleSRC' => array
		(
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('mandatory'=>true,'fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes']),
			'sql'                     => "binary(16) NULL"
		),
		'overwriteMeta' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['overwriteMeta'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50 clr'),
			'sql'                     => array('type' => 'boolean', 'default' => false)
		),
		'alt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['alt'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'imageTitle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['imageTitle'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'addEnclosure' => array
		(
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => array('type' => 'boolean', 'default' => false)
		),
		'enclosure' => array
		(
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'filesOnly'=>true, 'isDownloads'=>true, 'extensions'=>Contao\Config::get('allowedDownload'), 'mandatory'=>true),
			'sql'                     => "blob NULL"
		),
		'related' => array(
			'exclude'                 => false,
			'inputType'               => 'checkbox',
			'options_callback'        => array('tl_product', 'getProducts'),
			'eval'                    => array('includeBlankOption'=>true,'multiple'=>true),
			'sql'                     => "blob NULL"
		),
		'published' => array
		(
			'exclude'                 => true,
			'toggle'                  => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array('doNotCopy'=>true),
			'sql'                     => array('type' => 'boolean', 'default' => false)
		),
		'featured' => array
		(
			'exclude'                 => true,
			'toggle'                  => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => array('type' => 'boolean', 'default' => false)
		),
		'start' => array
		(
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'stop' => array
		(
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		)
	)
);

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
		$this->import('BackendUser', 'User');
	}

	/**
	 * Auto-generate the product alias if it has not been set yet
	 * @param mixed
	 * @param DataContainer
	 * @return string
	 * @throws Exception
	 */
	public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = StringUtil::standardize(String::restoreBasicEntities($dc->activeRecord->title));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_product WHERE alias=?")
								   ->execute($varValue);

		// Check whether the product alias exists
		if ($objAlias->numRows > 1 && !$autoAlias)
		{
			throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
		}

		// Add ID to alias
		if ($objAlias->numRows && $autoAlias)
		{
			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}

	/**
	 * Generate a song row and return it as HTML string
	 * @param array
	 * @return string
	 */
	public function generateProductsRow($arrRow)
	{
		$objImage = FilesModel::findByPk($arrRow['singleSRC']);

		if ($objImage !== null)
		{
			$strImage = Image::getHtml(Image::get($objImage->path, '60', '60', 'center_center'));
		}

		return '<div><div style="float:left; margin-right:10px;">'.$strImage.'</div><p><strong>'. $arrRow['title'].'</strong></p><p> Brand: '.$arrRow['brand'] .' &emsp; Model: '. $arrRow['model']. ' &emsp; SKU: '. $arrRow['sku'] . ' &emsp; Visit: '. $arrRow['visit'] .'</p></div>';
	}

	/**
	 * Get records from the master category
	 *
	 * @param	DataContainer
	 * @return	array
	 * @link	http://www.contao.org/callbacks.html#options_callback
	 */
	public function getProducts(DataContainer $dc)
	{

		$objItems = $this->Database->prepare("SELECT * FROM tl_product WHERE pid=? ORDER BY date DESC")->execute($dc->activeRecord->pid);

		while( $objItems->next() )
		{
			if ($objItems->id !== $dc->activeRecord->id) {

				$arrItems[$objItems->id] = $objItems->title;

				if($objItems->model) {
					$arrItems[$objItems->id] .= ' [model: ' . $objItems->model .']';
				}

				if ($objItems->sku) {
					$arrItems[$objItems->id] .= ' (sku: ' . $objItems->sku .')';
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
    //     if (!$this->User->isAdmin && !in_array('tl_product::categories', $this->User->alexf)) {

    //         // Return if the record is not new
    //         if ($dc->activeRecord->tstamp) {
    //             return;
    //         }

    //         $arrCategories = $this->User->productcategories_default;
    //     }

    //     $this->deleteCategories($dc);

    //     if (is_array($arrCategories) && !empty($arrCategories)) {
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
