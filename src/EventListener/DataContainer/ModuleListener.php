<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\EventListener\DataContainer;

use Contao\Backend;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;

class ModuleListener
{
    /**
     * Return all product templates as array.
     */
    #[AsCallback(table: 'tl_module', target: 'fields.product_template.options')]
    public function getProductTemplates(): array
    {
        return Backend::getTemplateGroup('product_');
    }
}
