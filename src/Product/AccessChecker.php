<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2026 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Product;

use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\StringUtil;
use Contao\System;
use Respinar\ProductsBundle\Model\CatalogModel;

final class AccessChecker
{
    /**
     * Sort out protected catalogs.
     */
    public static function sortOutProtected(array $catalogs): array
    {
        if ([] === $catalogs) {
            return $catalogs;
        }

        $catalogModel = CatalogModel::findMultipleByIds($catalogs);

        if (null === $catalogModel) {
            return [];
        }

        $security = System::getContainer()->get('security.helper');

        $allowedCatalogs = [];

        while ($catalogModel->next()) {
            if (
                $catalogModel->protected
                && !$security->isGranted(
                    ContaoCorePermissions::MEMBER_IN_GROUPS,
                    StringUtil::deserialize($catalogModel->groups, true)
                )
            ) {
                continue;
            }

            $allowedCatalogs[] = $catalogModel->id;
        }

        return $allowedCatalogs;
    }
}