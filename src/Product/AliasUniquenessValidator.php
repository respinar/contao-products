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

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

/**
 * Ensures the uniqueness of product aliases within the scope of a website root page.
 *
 * A product URL consists of the reader page (jumpTo) of its catalog and the
 * product alias. The reader page of a translated catalog belongs to another
 * website root page, therefore an alias has to be unique among all catalogs whose
 * reader page belongs to the same root page. The same alias may be used by the
 * translations on other websites.
 */
class AliasUniquenessValidator
{
    /**
     * @var array<int, int|null>
     */
    private array $rootPageCache = [];

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Returns the IDs of all catalogs whose reader page (jumpTo) belongs to the same
     * website root page as the given page.
     *
     * Falls back to the catalogs sharing the exact reader page if its root page
     * cannot be determined (e.g. because the page has been deleted).
     *
     * @return list<int>
     */
    public function getCatalogIdsForRootPage(int $pageId): array
    {
        $rootPageId = $this->getRootPageId($pageId);
        $catalogIds = [];

        foreach ($this->connection->fetchAllAssociative('SELECT id, jumpTo FROM tl_product_catalog') as $row) {
            $jumpTo = (int) $row['jumpTo'];

            if (null === $rootPageId) {
                // Fall back to the catalogs sharing the same reader page
                if ($jumpTo === $pageId) {
                    $catalogIds[] = (int) $row['id'];
                }
            } elseif ($jumpTo > 0 && $this->getRootPageId($jumpTo) === $rootPageId) {
                $catalogIds[] = (int) $row['id'];
            }
        }

        return $catalogIds;
    }

    /**
     * Groups all catalogs by the root page of their reader page (jumpTo).
     *
     * Catalogs without a resolvable root page are grouped under the key 0.
     *
     * @return array<int, list<int>>
     */
    public function getCatalogIdsByRootPage(): array
    {
        $grouped = [];

        foreach ($this->connection->fetchAllAssociative('SELECT id, jumpTo FROM tl_product_catalog') as $row) {
            $jumpTo = (int) $row['jumpTo'];
            $rootPageId = $jumpTo > 0 ? $this->getRootPageId($jumpTo) : null;

            $grouped[$rootPageId ?? 0][] = (int) $row['id'];
        }

        return $grouped;
    }

    /**
     * Finds the products with one of the given aliases in the given catalogs.
     *
     * @param list<string> $aliases
     * @param list<int>    $catalogIds
     *
     * @return list<array{id: int, alias: string, title: string, catalogTitle: string}>
     */
    public function findProductsByAliases(array $aliases, array $catalogIds, int $excludeProductId = 0, int $excludeCatalogId = 0): array
    {
        $aliases = array_values(array_unique(array_filter($aliases)));

        if ([] === $aliases || [] === $catalogIds) {
            return [];
        }

        $query = 'SELECT p.id, p.alias, p.title, c.title AS catalogTitle
                  FROM tl_product p
                  JOIN tl_product_catalog c ON c.id = p.pid
                  WHERE p.alias IN (:aliases) AND p.pid IN (:catalogIds)';
        $parameters = ['aliases' => $aliases, 'catalogIds' => $catalogIds];
        $types = [
            'aliases' => ArrayParameterType::STRING,
            'catalogIds' => ArrayParameterType::INTEGER,
        ];

        if ($excludeProductId > 0) {
            $query .= ' AND p.id != :excludeProductId';
            $parameters['excludeProductId'] = $excludeProductId;
        }

        if ($excludeCatalogId > 0) {
            $query .= ' AND p.pid != :excludeCatalogId';
            $parameters['excludeCatalogId'] = $excludeCatalogId;
        }

        $rows = $this->connection->fetchAllAssociative($query, $parameters, $types);

        return array_map(
            static fn (array $row): array => [
                'id' => (int) $row['id'],
                'alias' => (string) $row['alias'],
                'title' => (string) $row['title'],
                'catalogTitle' => (string) $row['catalogTitle'],
            ],
            $rows,
        );
    }

    /**
     * Returns the root page ID of the given page or null if it cannot be determined.
     */
    public function getRootPageId(int $pageId): int|null
    {
        if (\array_key_exists($pageId, $this->rootPageCache)) {
            return $this->rootPageCache[$pageId];
        }

        $currentId = $pageId;

        for ($i = 0; $i < 32 && $currentId > 0; ++$i) {
            $row = $this->connection->fetchAssociative(
                'SELECT pid, type FROM tl_page WHERE id = ?',
                [$currentId],
            );

            if (false === $row) {
                return $this->rootPageCache[$pageId] = null;
            }

            if ('root' === $row['type']) {
                return $this->rootPageCache[$pageId] = $currentId;
            }

            $currentId = (int) $row['pid'];
        }

        return $this->rootPageCache[$pageId] = null;
    }
}
