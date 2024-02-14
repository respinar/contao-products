<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\EventListener\DataContainer;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;

use Contao\Backend;

class ContentListener
{
    /**
     * Return all navigation templates as array.
     *
     * @return array
     */
    #[AsCallback(table: 'tl_content', target: 'fields.product_template.options')]
    public function getProductTemplates(): array
    {
        return Backend::getTemplateGroup('product_');
    }
}
