<?php

declare(strict_types=1);

namespace Respinar\ProductsBundle\EventListener\DataContainer;

use Contao\Backend;

final class ContentDcaListener
{
    /**
     * Return all navigation templates as array.
     *
     * @return array
     */
    public function getProductTemplates()
    {
        return Backend::getTemplateGroup('product_');
    }
}
