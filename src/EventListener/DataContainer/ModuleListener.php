<?php

declare(strict_types=1);

namespace Respinar\ProductsBundle\EventListener\DataContainer;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;

use Contao\Backend;
use Contao\Database;

class ModuleListener
{
	// public function __construct
    // (
    //     private readonly Database $Datebase
    // ){

	// }

    /**
     * Return all navigation templates as array.
     *
     * @return array
     */
	#[AsCallback(table: 'tl_module', target: 'fields.product_template.options')]
    public function getProductTemplates(): array
    {
        return Backend::getTemplateGroup('product_');
    }

    /**
	 * Return all related templates as array
	 *
	 * @return array
	 */
	#[AsCallback(table: 'tl_module', target: 'fields.related_template.options')]
	public function getRelatedTemplates(): array
	{
		return Backend::getTemplateGroup('related_');
	}

	/**
	 * Get all product detail modules and return them as array
	 * @return array
	 */
	#[AsCallback(table: 'tl_module', target: 'fields.product_detailModule.options')]
	public function getDetailModules(): array
	{
		$arrModules = array();
		$objModules = Database::getInstance()->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='product_detail' ORDER BY t.name, m.name");

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}
}
