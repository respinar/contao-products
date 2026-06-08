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
 * Stores the root page of the catalog's reader page (jumpTo) on all products, so
 * products can be found by alias and website without walking the page tree.
 */
class ProductRootPageMigration extends AbstractMigration
{
    public function __construct(
        private readonly Connection $connection,
        private readonly AliasUniquenessValidator $aliasUniqueness,
    ) {
    }

    public function shouldRun(): bool
    {
        return [] !== $this->findOutdatedCatalogs();
    }

    public function run(): MigrationResult
    {
        $updated = 0;

        foreach ($this->findOutdatedCatalogs() as $catalogId => $rootPageId) {
            $updated += $this->connection->executeStatement(
                'UPDATE tl_product SET rootPageId = :rootPageId WHERE pid = :catalogId AND rootPageId != :rootPageId',
                ['rootPageId' => $rootPageId, 'catalogId' => $catalogId],
            );
        }

        return $this->createResult(true, \sprintf('Updated the root page of %d product(s).', $updated));
    }

    /**
     * Returns the expected root page ID for catalogs whose products are out of sync.
     *
     * @return array<int, int>
     */
    private function findOutdatedCatalogs(): array
    {
        if (!$this->columnExists()) {
            return [];
        }

        $outdated = [];

        foreach ($this->aliasUniqueness->getCatalogIdsByRootPage() as $rootPageId => $catalogIds) {
            // Skip catalogs with an unresolvable root page, their products stay at 0
            if (0 === $rootPageId) {
                continue;
            }

            $count = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM tl_product WHERE pid IN (:catalogIds) AND rootPageId != :rootPageId',
                ['catalogIds' => $catalogIds, 'rootPageId' => $rootPageId],
                ['catalogIds' => ArrayParameterType::INTEGER],
            );

            if ($count > 0) {
                foreach ($catalogIds as $catalogId) {
                    $outdated[$catalogId] = $rootPageId;
                }
            }
        }

        return $outdated;
    }

    /**
     * The column is created by the schema update, which runs before the migrations.
     */
    private function columnExists(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_product', 'tl_product_catalog'])) {
            return false;
        }

        return \array_key_exists(
            'rootpageid',
            array_change_key_case($schemaManager->listTableColumns('tl_product'), CASE_LOWER),
        );
    }
}
