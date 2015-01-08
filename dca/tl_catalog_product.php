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
 * Table tl_catalog_product
 */
$GLOBALS['TL_DCA']['tl_catalog_product'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_catalog_category',
		'ctable'                      => array('tl_catalog_type','tl_content'),
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'onload_callback'             => array
		(
			array('tl_catalog_product', 'showSelectbox'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'    => 'primary',
				'pid'   => 'index',
				'alias' => 'index'
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
			'headerFields'            => array('title','jumpTo','protected'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_catalog_product', 'generateProductsRow')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['edit'],
				'href'                => 'table=tl_content',
				'icon'                => 'edit.gif'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['editmeta'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif'
			),
			'type' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['type'],
				'href'                => 'table=tl_catalog_type',
				'icon'                => 'system/modules/catalog/assets/type.png'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_catalog_product', 'toggleIcon')
			),
			'feature' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['feature'],
				'icon'                => 'featured.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleFeatured(this,%s)"',
				'button_callback'     => array('tl_catalog_product', 'iconFeatured')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_product']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('addEnclosure','published'),
		'default'                     => '{title_legend},title,alias,model,date,featured;
		                                  {image_legend},singleSRC;
		                                  {meta_legend},keywords,description;
		                                  {feature_legend},features;
		                                  {spec_legend},specs;
		                                  {related_legend},related;
		                                  {enclosure_legend:hide},addEnclosure;
		                                  {publish_legend},published'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'addEnclosure'                => 'enclosure',
		'published'                   => 'start,stop'
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>128),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['alias'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'alias','unique'=>true,'maxlength'=>128, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array('tl_catalog_product', 'generateAlias')
			),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'languageMain' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['languageMain'],
			'exclude'                 => false,
			'inputType'               => 'select',
			'options_callback'        => array('tl_catalog_product', 'getMasterCategory'),
			'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'model' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['model'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['date'],
			'default'                 => time(),
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'keywords' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['keywords'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'search'                  => true,
			'eval'                    => array('decodeEntities'=>true),
			'sql'                     => "text NULL"
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['description'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'search'                  => true,
			'eval'                    => array('rte'=>'tinyMCE','tl_class'=>'clr'),
			'sql'                     => "text NULL"
		),
		'features' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['features'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'listWizard',
			'eval'                    => array(),
			'sql'                     => "blob NULL",
		),
		'specs' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['specs'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
			(
				'columnFields' => array
				(
					'spectitle' => array
					(
						'label'       => &$GLOBALS['TL_LANG']['tl_catalog_product']['spectitle'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array('style'=>'width:280px'),
					),
					'specvalue' => array
					(
						'label'       => &$GLOBALS['TL_LANG']['tl_catalog_product']['specvalue'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array('style'=>'width:280px'),
					)
				)
			),
			'sql'                     => "blob NULL",
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('mandatory'=>true,'fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes']),
			'sql'                     => "binary(16) NULL"
		),
		'addEnclosure' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['addEnclosure'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'enclosure' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['enclosure'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'filesOnly'=>true, 'isDownloads'=>true, 'extensions'=>Config::get('allowedDownload'), 'mandatory'=>true),
			'sql'                     => "blob NULL"
		),
		'related' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['related'],
			'exclude'                 => false,
			'inputType'               => 'checkbox',
			'options_callback'        => array('tl_catalog_product', 'getProducts'),
			'eval'                    => array('includeBlankOption'=>true,'multiple'=>true),
			'sql'                     => "blob NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array('doNotCopy'=>true,'submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'featured' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['featured'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'start' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['start'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'stop' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_product']['stop'],
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
class tl_catalog_product extends Backend
{

	/**
	 * Auto-generate the product alias if it has not been set yet
	 * @param mixed
	 * @param \DataContainer
	 * @return string
	 * @throws \Exception
	 */
	public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = standardize(String::restoreBasicEntities($dc->activeRecord->title));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_catalog_product WHERE alias=?")
								   ->execute($varValue);

		// Check whether the news alias exists
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
		$objImage = \FilesModel::findByPk($arrRow['singleSRC']);

		if ($objImage !== null)
		{
			$strImage = \Image::getHtml(\Image::get($objImage->path, '80', '60', 'center_center'));
		}

		return '<div><div style="float:left; margin-right:10px;">'.$strImage.'</div>'. $arrRow['title'] . '</div>';
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

		$this->createInitialVersion('tl_catalog_product', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_catalog_product']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_catalog_product']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_catalog_product SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$this->createNewVersion('tl_catalog_product', $intId);

	}

	/**
	 * Return the "feature/unfeature element" button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function iconFeatured($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(Input::get('fid')))
		{
			$this->toggleFeatured(Input::get('fid'), (Input::get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the fid, so hacking attempts are logged
		//if (!$this->User->hasAccess('tl_catalog_product::featured', 'alexf'))
		//{
			//return '';
		//}

		$href .= '&amp;fid='.$row['id'].'&amp;state='.($row['featured'] ? '' : 1);

		if (!$row['featured'])
		{
			$icon = 'featured_.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}


	/**
	 * Feature/unfeature a news item
	 * @param integer
	 * @param boolean
	 * @return string
	 */
	public function toggleFeatured($intId, $blnVisible)
	{
		// Check permissions to edit
		Input::setGet('id', $intId);
		Input::setGet('act', 'feature');
		$this->checkPermission();

		// Check permissions to feature
		//if (!$this->User->hasAccess('tl_catalog_product::featured', 'alexf'))
		//{
			//$this->log('Not enough permissions to feature/unfeature news item ID "'.$intId.'"', __METHOD__, TL_ERROR);
			//$this->redirect('contao/main.php?act=error');
		//}

		$objVersions = new Versions('tl_news', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_catalog_product']['fields']['featured']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_catalog_product']['fields']['featured']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, $this);
				}
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_catalog_product SET tstamp=". time() .", featured='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$objVersions->create();
		$this->log('A new version of record "tl_catalog_product.id='.$intId.'" has been created'.$this->getParentEntries('tl_news', $intId), __METHOD__, TL_GENERAL);
	}


	/**
	 * Get records from the master category
	 *
	 * @param	DataContainer
	 * @return	array
	 * @link	http://www.contao.org/callbacks.html#options_callback
	 */
	public function getMasterCategory(DataContainer $dc)
	{
		$sameDay = $GLOBALS['TL_LANG']['tl_catalog_product']['sameDay'];
		$otherDay = $GLOBALS['TL_LANG']['tl_catalog_product']['otherDay'];

		$arrItems = array($sameDay => array(), $otherDay => array());
		$objItems = $this->Database->prepare("SELECT * FROM tl_catalog_product WHERE pid=(SELECT tl_catalog_category.master FROM tl_catalog_category LEFT OUTER JOIN tl_catalog_product ON tl_catalog_product.pid=tl_catalog_category.id WHERE tl_catalog_product.id=?) ORDER BY date DESC")->execute($dc->id);

		$dayBegin = strtotime('0:00', $dc->activeRecord->date);

		while( $objItems->next() )
		{
			if (strtotime('0:00', $objItems->date) == $dayBegin)
			{
				$arrItems[$sameDay][$objItems->id] = $objItems->title . ' (' . $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $objItems->time) . ')';
			}
			else
			{
				$arrItems[$otherDay][$objItems->id] = $objItems->title . ' (' . $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $objItems->time) . ')';
			}
		}

		return $arrItems;
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

		$objItems = $this->Database->prepare("SELECT * FROM tl_catalog_product WHERE pid=? ORDER BY date DESC")->execute($dc->activeRecord->pid);

		while( $objItems->next() )
		{
				$arrItems[$objItems->id] = $objItems->title . ' (' . $objItems->model . ')' ;
		}

		return $arrItems;
	}


	/**
	 * Show the select menu only on slave archives
	 *
	 * @param	DataContainer
	 * @return	void
	 * @link	http://www.contao.org/callbacks.html#onload_callback
	 */
	public function showSelectbox(DataContainer $dc)
	{
		if($this->Input->get('act') == "edit")
		{
			$objCategory = $this->Database->prepare("SELECT tl_catalog_category.* FROM tl_catalog_category LEFT OUTER JOIN tl_catalog_product ON tl_catalog_product.pid=tl_catalog_category.id WHERE tl_catalog_product.id=?")
										 ->limit(1)
										 ->execute($dc->id);

			if($objCategory->numRows && $objCategory->master > 0)
			{
				$GLOBALS['TL_DCA']['tl_catalog_product']['palettes']['default'] = preg_replace('@([,|;])(alias[,|;])@','$1languageMain,$2', $GLOBALS['TL_DCA']['tl_catalog_product']['palettes']['default']);
				$GLOBALS['TL_DCA']['tl_catalog_product']['fields']['title']['eval']['tl_class'] = 'w50';
				$GLOBALS['TL_DCA']['tl_catalog_product']['fields']['alias']['eval']['tl_class'] = 'clr w50';
			}
		}
		else if($this->Input->get('act') == "editAll")
		{
			$GLOBALS['TL_DCA']['tl_catalog_product']['palettes']['regular'] = preg_replace('@([,|;]{1}language)([,|;]{1})@','$1,languageMain$2', $GLOBALS['TL_DCA']['tl_catalog_product']['palettes']['regular']);
		}
	}


}
