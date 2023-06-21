<?php

declare(strict_types=1);

namespace Respinar\ProductsBundle\EventListener\DataContainer;

use Contao\Backend;
use Contao\Database;

final class ModuleDcaListener
{
	public function __construct
    (
        private readonly Database $Datebase
    ){

	}

    /**
     * Return all navigation templates as array.
     *
     * @return array
     */
    public function getProductTemplates()
    {
        return Backend::getTemplateGroup('product_');
    }

    /**
	 * Return all related templates as array
	 *
	 * @return array
	 */
	public function getRelatedTemplates()
	{
		return Backend::getTemplateGroup('related_');
	}

	/**
	 * Get all product detail modules and return them as array
	 * @return array
	 */
	public function getDetailModules()
	{
		$arrModules = array();
		$objModules = $this->Datebase->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='product_detail' ORDER BY t.name, m.name");

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}
}
