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

use Contao\BackendUser;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Respinar\ProductsBundle\Product\AliasUniquenessValidator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Data container callbacks for the tl_product table.
 */
class ProductListener
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Security $security,
        private readonly AliasUniquenessValidator $aliasUniqueness,
    ) {
    }

    /**
     * Check permissions for non-admin users.
     */
    #[AsCallback(table: 'tl_product', target: 'config.onload')]
    public function checkPermission(): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof BackendUser || $user->isAdmin) {
            return;
        }

        if (!\is_array($user->products) || [] === $user->products) {
            $user->products = [0];
        }

        $act = Input::get('act');
        $id = Input::get('id');

        switch ($act) {
            case 'create':
                if (!\in_array(Input::get('pid') ?? $id, $user->products, true)) {
                    throw new AccessDeniedException('Not enough permissions to create products in this catalog.');
                }

                break;

            case 'edit':
            case 'copy':
            case 'cut':
            case 'delete':
            case 'show':
            case 'toggle':
                $pid = $this->connection->fetchOne('SELECT pid FROM tl_product WHERE id = ?', [$id]);

                if (false === $pid || !\in_array($pid, $user->products, true)) {
                    throw new AccessDeniedException('Not enough permissions to '.$act.' product ID '.$id.'.');
                }

                break;

            case 'paste':
                if (!\in_array(Input::get('pid'), $user->products, true)) {
                    throw new AccessDeniedException('Not enough permissions to paste products into this catalog.');
                }

                break;
        }
    }

    /**
     * Auto-generate the product alias if it has not been set yet.
     *
     * The alias only has to be unique on the website (root page of the reader page)
     * the product belongs to, so the same alias may be used by the translations on
     * other websites.
     */
    #[AsCallback(table: 'tl_product', target: 'fields.alias.save')]
    public function generateAlias(string $varValue, DataContainer $dc): string
    {
        $autoAlias = false;

        // Generate alias if there is none
        if ('' === $varValue) {
            $autoAlias = true;
            $varValue = StringUtil::standardize(
                StringUtil::restoreBasicEntities($dc->activeRecord->title),
            );
        }

        // The alias must be unique among all products whose catalog points to a reader
        // page (jumpTo) under the same root page as the current product
        $catalogIds = $this->aliasUniqueness->getCatalogIdsForRootPage(
            (int) $this->connection->fetchOne(
                'SELECT jumpTo FROM tl_product_catalog WHERE id = ?',
                [(int) $dc->activeRecord->pid],
            ),
        );

        $findConflicts = fn (string $alias): array => $this->aliasUniqueness->findProductsByAliases(
            [$alias],
            $catalogIds,
            (int) $dc->id,
        );

        if ($autoAlias) {
            // Make sure the generated alias is unique on the same website
            $base = $varValue;
            $i = 0;

            while ([] !== $findConflicts($varValue)) {
                $varValue = $base.'-'.++$i;
            }
        } elseif ([] !== $conflicts = $findConflicts($varValue)) {
            throw new \RuntimeException(\sprintf($GLOBALS['TL_LANG']['ERR']['aliasUsedOnWebsite'], $varValue, $conflicts[0]['title'], $conflicts[0]['catalogTitle']));
        }

        return $varValue;
    }

    /**
     * Get products from the parent catalog to link as related products.
     *
     * @return array<int, string>
     */
    #[AsCallback(table: 'tl_product', target: 'fields.related.options')]
    public function getProducts(DataContainer $dc): array
    {
        $arrItems = [];

        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, title, model, sku FROM tl_product WHERE pid = ? ORDER BY date DESC',
            [$dc->activeRecord->pid],
        );

        foreach ($rows as $row) {
            if ($row['id'] !== $dc->activeRecord->id) {
                $label = $row['title'];

                if ($row['model']) {
                    $label .= ' [model: '.$row['model'].']';
                }

                if ($row['sku']) {
                    $label .= ' (sku: '.$row['sku'].')';
                }

                $arrItems[$row['id']] = $label;
            }
        }

        return $arrItems;
    }

    /**
     * Get products from the master catalog to link as language main.
     *
     * Only offers master products that have not already been assigned to another
     * product in the same catalog, so each master product can only be translated once
     * per language.
     */
    #[AsCallback(table: 'tl_product', target: 'fields.languageMain.options')]
    public function getLanguageMainOptions(DataContainer $dc): array
    {
        if (null === $dc->activeRecord) {
            return [];
        }

        $catalog = $this->connection->fetchAssociative('SELECT * FROM tl_product_catalog WHERE id = ?', [$dc->activeRecord->pid]);

        if (false === $catalog) {
            return [];
        }

        // Use the master catalog (or the current catalog if it has none)
        $intMaster = (int) $catalog['master'] ?: (int) $catalog['id'];
        $intCurrent = (int) $dc->activeRecord->id;

        // Exclude master products that are already used by another product in this catalog
        $usedIds = array_map(
            intval(...),
            $this->connection->fetchFirstColumn(
                'SELECT languageMain FROM tl_product WHERE pid = ? AND id != ? AND languageMain != 0',
                [$dc->activeRecord->pid, $intCurrent],
            ),
        );

        $arrOptions = [];
        $rows = $this->connection->fetchAllAssociative('SELECT id, title, model, sku FROM tl_product WHERE pid = ? ORDER BY title', [$intMaster]);

        foreach ($rows as $row) {
            $id = (int) $row['id'];

            if ($id === (int) $dc->activeRecord->id || \in_array($id, $usedIds, true)) {
                continue;
            }

            $label = $row['title'];

            if ($row['model']) {
                $label .= ' [model: '.$row['model'].']';
            }

            if ($row['sku']) {
                $label .= ' (sku: '.$row['sku'].')';
            }

            $arrOptions[$id] = $label;
        }

        return $arrOptions;
    }
}
