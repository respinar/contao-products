<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Respinar\ProductsBundle\Product\AliasUniquenessValidator;

/**
 * Reports products whose alias is used more than once on the same website root page.
 */
class ProductAliasUniquenessMigration extends AbstractMigration
{
    private const MAX_CONFLICTS = 20;

    public function __construct(
        private readonly Connection $connection,
        private readonly AliasUniquenessValidator $aliasUniqueness,
    ) {
    }

    public function shouldRun(): bool
    {
        return [] !== $this->findConflicts();
    }

    public function run(): MigrationResult
    {
        $conflicts = $this->findConflicts();

        if ([] === $conflicts) {
            return $this->createResult(true);
        }

        return $this->createResult(true, \sprintf(
            'Found %d product alias(es) that are used more than once on the same website root page: %s. The affected products have to be renamed manually, because changing aliases automatically would break existing URLs.',
            \count($conflicts),
            implode(', ', \array_slice($conflicts, 0, self::MAX_CONFLICTS)).(\count($conflicts) > self::MAX_CONFLICTS ? ', …' : ''),
        ));
    }

    /**
     * @return list<string>
     */
    private function findConflicts(): array
    {
        $conflicts = [];

        foreach ($this->aliasUniqueness->getCatalogIdsByRootPage() as $rootPageId => $catalogIds) {
            // Skip catalogs with an unresolvable root page
            if (0 === $rootPageId || 2 > \count($catalogIds)) {
                continue;
            }

            $duplicates = $this->findDuplicateAliases($catalogIds);

            if ([] === $duplicates) {
                continue;
            }

            $titles = $this->getCatalogTitles(array_merge(...array_values($duplicates)));

            foreach ($duplicates as $alias => $duplicateCatalogIds) {
                $conflicts[] = \sprintf(
                    '%s (%s)',
                    $alias,
                    implode(', ', array_map(
                        static fn (int $id): string => $titles[$id] ?? (string) $id,
                        $duplicateCatalogIds,
                    )),
                );
            }
        }

        return $conflicts;
    }

    /**
     * @param list<int> $catalogIds
     *
     * @return array<string, list<int>>
     */
    private function findDuplicateAliases(array $catalogIds): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT p.alias, GROUP_CONCAT(DISTINCT p.pid) AS catalogIds
             FROM tl_product p
             WHERE p.pid IN (:catalogIds)
             GROUP BY p.alias
             HAVING COUNT(*) > 1',
            ['catalogIds' => $catalogIds],
            ['catalogIds' => ArrayParameterType::INTEGER],
        );

        $duplicates = [];

        foreach ($rows as $row) {
            $duplicates[(string) $row['alias']] = array_map(
                intval(...),
                explode(',', (string) $row['catalogIds']),
            );
        }

        return $duplicates;
    }

    /**
     * @param list<int> $catalogIds
     *
     * @return array<int, string>
     */
    private function getCatalogTitles(array $catalogIds): array
    {
        $titles = [];

        foreach ($this->connection->fetchAllAssociative(
            'SELECT id, title FROM tl_product_catalog WHERE id IN (:catalogIds)',
            ['catalogIds' => $catalogIds],
            ['catalogIds' => ArrayParameterType::INTEGER],
        ) as $row) {
            $titles[(int) $row['id']] = (string) $row['title'];
        }

        return $titles;
    }
}
