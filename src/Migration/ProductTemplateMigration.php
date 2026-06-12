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

/**
 * Renames the stored product templates that were renamed in the bundle
 * (product_short to product_card and product_simple to product_compact).
 */
class ProductTemplateMigration extends AbstractMigration
{
    private const MAPPING = [
        'product_simple' => 'product_compact',
        'product_short' => 'product_card',
    ];

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function shouldRun(): bool
    {
        return [] !== $this->findOutdatedTables();
    }

    public function run(): MigrationResult
    {
        $updated = 0;

        foreach ($this->findOutdatedTables() as $table) {
            foreach (self::MAPPING as $old => $new) {
                $updated += $this->connection->executeStatement(
                    "UPDATE $table SET product_template = :new WHERE product_template = :old",
                    ['new' => $new, 'old' => $old],
                );
            }
        }

        return $this->createResult(true, \sprintf('Updated the template of %d record(s).', $updated));
    }

    /**
     * @return array<string>
     */
    private function findOutdatedTables(): array
    {
        $tables = [];

        foreach (['tl_content', 'tl_module'] as $table) {
            if (!$this->connection->createSchemaManager()->tablesExist([$table])) {
                continue;
            }

            $count = (int) $this->connection->fetchOne(
                "SELECT COUNT(*) FROM $table WHERE product_template IN (:names)",
                ['names' => array_keys(self::MAPPING)],
                ['names' => ArrayParameterType::STRING],
            );

            if ($count > 0) {
                $tables[] = $table;
            }
        }

        return $tables;
    }
}
