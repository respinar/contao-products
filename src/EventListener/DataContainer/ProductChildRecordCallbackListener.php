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

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\FilesModel;
use Contao\Image;
use Contao\StringUtil;

#[AsCallback(table: 'tl_product', target: 'list.sorting.child_record')]
class ProductChildRecordCallbackListener
{
    /**
     * @param array<string, mixed> $row
     */
    public function __invoke(array $row): string
    {
        $featuredImage = '';

        if (!empty($row['singleSRC'])) {
            $fileModel = FilesModel::findByUuid($row['singleSRC']);

            if (null !== $fileModel) {
                $featuredImage = Image::getHtml(
                    $fileModel->path,
                    '',
                    'style="max-width:50px; max-height:50px; margin-right:8px; vertical-align:middle; object-fit:contain;"',
                );
            }
        }

        return \sprintf(
            '<div class="tl_content_left">%s%s</div>',
            $featuredImage,
            StringUtil::specialchars((string) ($row['title'] ?? '')),
        );
    }
}
