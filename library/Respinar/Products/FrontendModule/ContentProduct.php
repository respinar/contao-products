<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   product
 * @author    Hamid Abbaszadeh
 * @license   LGPL-3.0+
 * @copyright 2014-2016
 */


/**
 * Namespace
 */
namespace Respinar\Products;


/**
 * Class ContentProduct
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ContentProduct extends \ModuleProduct
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_product';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['product'][0]) . ' ###';

			$objProduct = \ProductModel::findPublishedByIdOrAlias($this->product);

			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $objProduct->title;
			$objTemplate->href = 'contao/main.php?do=catalogs&amp;table=tl_product&amp;act=edit&amp;id=' . $this->id;

			$objFile = \FilesModel::findByUuid($objProduct->singleSRC);

			$objTemplate->singleSRC = $objFile->path;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		$objProduct = \ProductModel::findPublishedByIdOrAlias($this->product);

		if (null === $objProduct)
		{
			echo "not found";
		}		

		$this->Template->setData($objProduct->row());

		$this->Template->link =  $this->generateProductUrl($objProduct, $blnAddCategory);

		if (time() - $objProduct->date < 2592000) {
			$this->Template->new_product = true;
		}	

		$arrMeta = $this->getMetaFields($objProduct);

		// Add the meta information
		$this->Template->date = $arrMeta['date'];
		$this->Template->meta_brand = $arrMeta['brand'];
		$this->Template->meta_model = $arrMeta['model'];
		$this->Template->meta_code = $arrMeta['code'];
		$this->Template->meta_sku = $arrMeta['sku'];
		$this->Template->meta_buy = $arrMeta['buy'];

		$this->Template->hasMetaFields = !empty($arrMeta);
		$this->Template->timestamp = $objProduct->date;
		$this->Template->datetime = date('Y-m-d\TH:i:sP', $objProduct->date);
		

		// Add an image
		if ($objProduct->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objProduct->singleSRC);			

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objProduct->singleSRC))
				{
					$this->template->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{			

				// Do not override the field now that we have a model registry (see #6303)
				$arrProduct = $objProduct->row();

				// Override the default image size
				if ($this->size != '')
				{
					$size = deserialize($this->size);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrProduct['size'] = $this->size;
					}
				}

				$arrProduct['singleSRC'] = $objModel->path;
				$strLightboxId = 'lightbox[lb' . $objProduct->id . ']';
				$arrProduct['fullsize'] = $this->fullsize;
				$this->addImageToTemplate($this->Template, $arrProduct, null, $strLightboxId);
			}
		}


		

	}
}