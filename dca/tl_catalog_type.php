<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   catalog
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL
 * @copyright 2014
 */


/**
 * Table tl_catalog_type
 */
$GLOBALS['TL_DCA']['tl_catalog_type'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_catalog_product',
		'sql' => array
		(
			'keys' => array
			(
				'id'  => 'primary',
				'pid' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('sorting'),
			'headerFields'            => array('title'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_catalog_type', 'generateProductsRow')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_type']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_type']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_type']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_type']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_type']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_catalog_type', 'toggleIcon')
			),
			'stock' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_type']['stock'],
				'icon'                => 'featured.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleStock(this,%s)"',
				'button_callback'     => array('tl_catalog_type', 'iconStock')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_type']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},title,code,price;
		                                  {spec_legend},spec;
		                                  {image_legend},image;
		                                  {description_legend:hide},description;
		                                  {publish_legend},published'
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
			'foreignKey'              => 'tl_catalog_category.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_type']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'code' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_type']['code'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'alias','unique'=>true,'maxlength'=>6, 'tl_class'=>'w50'),
			'sql'                     => "varchar(6) NOT NULL default ''"
		),
		'price' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_type']['price'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true,'rgxp'=>'digit', 'maxlength'=>12, 'tl_class'=>'w50'),
			'sql'                     => "int(12) NOT NULL default '0'"
		),
		'spec' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_type']['spec'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
			(
				'columnFields' => array
				(
					'spectitle' => array
					(
						'label'       => &$GLOBALS['TL_LANG']['tl_catalog_type']['spectitle'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array('style'=>'width:280px'),
					),
					'specvalue' => array
					(
						'label'       => &$GLOBALS['TL_LANG']['tl_catalog_type']['specvalue'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array('style'=>'width:280px'),
					)
				)
			),
			'sql'                     => "blob NULL",
		),
		'image' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_type']['image'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('mandatory'=>true,'fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes']),
			'sql'                     => "binary(16) NULL"
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_type']['description'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'eval'                    => array('rte'=>'tinyMCE'),
			'sql'                     => "text NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_type']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array('doNotCopy'=>true, 'tl_class'=>'w50'),
			'sql'                     => "char(1) NOT NULL default ''"
		)
	)
);

/**
 * Provide miscellaneous methods that are used by the data configuration array
 */
class tl_catalog_type extends Backend
{

	/**
	 * Generate a song row and return it as HTML string
	 * @param array
	 * @return string
	 */
	public function generateProductsRow($arrRow)
	{
		$objImage = \FilesModel::findByPk($arrRow['image']);

		if ($objImage !== null)
		{
			$strImage = \Image::getHtml(\Image::get($objImage->path, '80', '60', 'center_center'));
		}

		return '<div><div style="float:left; margin-right:10px;">'.$strImage.'</div>'. $arrRow['title'] . '<br /><span style="padding-left:3px;color:#b3b3b3;">کد: ' . $arrRow['code'] . '<br>قیمت: '. number_format($arrRow[price]) .' ریال</span></div>';
	}

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		//if (!$this->User->isAdmin && !$this->User->hasAccess('tl_prices::published', 'alexf'))
		//{
		//	return '';
		//}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}



	public function toggleVisibility($intId, $blnVisible)
	{
		// Check permissions to edit
		$this->Input->setGet('id', $intId);
		$this->Input->setGet('act', 'toggle');
		//$this->checkPermission();

		// Check permissions to publish
		//if (!$this->User->isAdmin && !$this->User->hasAccess('tl_news::published', 'alexf'))
		//{
		//	$this->log('Not enough permissions to publish/unpublish news item ID "'.$intId.'"', 'tl_news toggleVisibility', TL_ERROR);
		//	$this->redirect('contao/main.php?act=error');
		//}

		$this->createInitialVersion('tl_catalog_type', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_catalog_type']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_catalog_type']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_catalog_type SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$this->createNewVersion('tl_catalog_type', $intId);

	}

	public function iconStock($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen($this->Input->get('sid')))
		{
			$this->toggleStock($this->Input->get('sid'), ($this->Input->get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		//if (!$this->User->isAdmin && !$this->User->hasAccess('tl_prices::published', 'alexf'))
		//{
		//	return '';
		//}

		$href .= '&amp;sid='.$row['id'].'&amp;state='.($row['stock'] ? '' : 1);

		if (!$row['stock'])
		{
			$icon = 'featured_.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}



	public function toggleStock($intId, $blnStock)
	{
		// Check permissions to edit
		$this->Input->setGet('id', $intId);
		$this->Input->setGet('act', 'stock');
		//$this->checkPermission();

		// Check permissions to publish
		//if (!$this->User->isAdmin && !$this->User->hasAccess('tl_news::published', 'alexf'))
		//{
		//	$this->log('Not enough permissions to publish/unpublish news item ID "'.$intId.'"', 'tl_news toggleVisibility', TL_ERROR);
		//	$this->redirect('contao/main.php?act=error');
		//}

		$this->createInitialVersion('tl_catalog_type', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_catalog_type']['fields']['stock']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_catalog_type']['fields']['stock']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnStock = $this->$callback[0]->$callback[1]($blnStock, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_catalog_type SET tstamp=". time() .", stock='" . ($blnStock ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$this->createNewVersion('tl_catalog_type', $intId);

	}

}
