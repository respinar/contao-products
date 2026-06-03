<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Product;

use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\StringUtil;
use Respinar\ProductsBundle\Model\CatalogModel;
use Symfony\Bundle\SecurityBundle\Security;

final class AccessChecker
{
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * Sort out protected catalogs.
     *
     * @param array<int> $catalogs
     *
     * @return array<int>
     */
    public function sortOutProtected(array $catalogs): array
    {
        if ([] === $catalogs) {
            return $catalogs;
        }

        $catalogModel = CatalogModel::findMultipleByIds($catalogs);

        if (null === $catalogModel) {
            return [];
        }

        $allowedCatalogs = [];

        while ($catalogModel->next()) {
            if (
                $catalogModel->protected
                && !$this->security->isGranted(
                    ContaoCorePermissions::MEMBER_IN_GROUPS,
                    StringUtil::deserialize($catalogModel->groups, true),
                )
            ) {
                continue;
            }

            $allowedCatalogs[] = $catalogModel->id;
        }

        return $allowedCatalogs;
    }
}
