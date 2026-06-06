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
use Doctrine\DBAL\Connection;
use Respinar\ProductsBundle\Product\AliasUniquenessValidator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Data container callbacks for the tl_product_catalog table.
 */
class ProductCatalogListener
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly Connection $connection,
        private readonly AliasUniquenessValidator $aliasUniqueness,
    ) {
    }

    /**
     * Check permissions for non-admin users.
     */
    #[AsCallback(table: 'tl_product_catalog', target: 'config.onload')]
    public function checkPermission(): void
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof BackendUser || $user->isAdmin) {
            return;
        }

        if (!\is_array($user->products) || empty($user->products)) {
            $user->products = [0];
        }

        $GLOBALS['TL_DCA']['tl_product_catalog']['list']['sorting']['root'] = $user->products;

        $act = Input::get('act');
        $id = Input::get('id');

        switch ($act) {
            case 'create':
            case 'select':
                if (!\is_array($user->productp) || !\in_array('create', $user->productp, true)) {
                    throw new AccessDeniedException('Not enough permissions to create product catalogs.');
                }

                break;

            case 'edit':
                if (
                    !\in_array($id, $user->products, true)
                    || !\is_array($user->productp)
                    || !\in_array('edit', $user->productp, true)
                ) {
                    throw new AccessDeniedException('Not enough permissions to edit product catalog ID '.$id.'.');
                }

                break;

            case 'copy':
                if (
                    !\in_array($id, $user->products, true)
                    || !\is_array($user->productp)
                    || !\in_array('create', $user->productp, true)
                ) {
                    throw new AccessDeniedException('Not enough permissions to copy product catalog ID '.$id.'.');
                }

                break;

            case 'delete':
                if (
                    !\in_array($id, $user->products, true)
                    || !\is_array($user->productp)
                    || !\in_array('delete', $user->productp, true)
                ) {
                    throw new AccessDeniedException('Not enough permissions to delete product catalog ID '.$id.'.');
                }

                break;

            case 'show':
                if (!\in_array($id, $user->products, true)) {
                    throw new AccessDeniedException('Not enough permissions to view product catalog ID '.$id.'.');
                }

                break;
        }
    }

    /**
     * Return all catalogs that can be used as the master catalog.
     *
     * @return array<int, string>
     */
    #[AsCallback(table: 'tl_product_catalog', target: 'fields.master.options')]
    public function getMasterCatalogs(DataContainer $dc): array
    {
        $options = [];

        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, title FROM tl_product_catalog WHERE id != ? ORDER BY title',
            [$dc->id],
        );

        foreach ($rows as $row) {
            $options[$row['id']] = $row['title'];
        }

        return $options;
    }

    /**
     * Validate that changing the reader page does not create duplicate product
     * aliases on the website (root page) of the new reader page.
     */
    #[AsCallback(table: 'tl_product_catalog', target: 'fields.jumpTo.save')]
    public function validateJumpTo(string $varValue, DataContainer $dc): string
    {
        $jumpTo = (int) $varValue;

        // Nothing to validate without a reader page, on a new catalog or without products
        if ($jumpTo <= 0 || null === $dc->activeRecord) {
            return $varValue;
        }

        $products = $this->connection->fetchAllAssociative(
            'SELECT alias FROM tl_product WHERE pid = ?',
            [(int) $dc->id],
        );

        if ([] === $products) {
            return $varValue;
        }

        // The products of the current catalog are excluded, they cannot conflict with
        // each other because the alias is unique per catalog in the database
        $conflicts = $this->aliasUniqueness->findProductsByAliases(
            array_column($products, 'alias'),
            $this->aliasUniqueness->getCatalogIdsForRootPage($jumpTo),
            0,
            (int) $dc->id,
        );

        if ([] === $conflicts) {
            return $varValue;
        }

        $labels = [];

        foreach (\array_slice($conflicts, 0, 10) as $conflict) {
            $labels[] = \sprintf('"%s" (%s)', $conflict['alias'], $conflict['catalogTitle']);
        }

        if (\count($conflicts) > 10) {
            $labels[] = '…';
        }

        throw new \RuntimeException(\sprintf($GLOBALS['TL_LANG']['ERR']['jumpToAliasConflict'], implode(', ', $labels)));
    }
}
