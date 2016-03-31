<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\TableDefinition\TableDefinition;
use PruneMazui\DdlGenerator\TableDefinition\Table;
use PruneMazui\DdlGenerator\TableDefinition\Schema;

/**
 * interface for DDL Builder
 * @author ko_tanaka
 */
interface DdlBuilderInterface
{
    /**
     * build all query
     *
     * @param TableDefinition $definition
     * @param boolean optional $add_drop_table
     * @return string
     */
    public function buildAll(TableDefinition $definition, $add_drop_table = true);

    /**
     * build all create table query
     *
     * @param TableDefinition $definition
     * @return string
     */
    public function buildAllCreateTable(TableDefinition $definition);

    /**
     * build all  drop table query
     *
     * @param TableDefinition $definition
     * @return string
     */
    public function buildAllDropTable(TableDefinition $definition);

    /**
     * build single table create query
     *
     * @param Schema $schema
     * @param Table $table
     * @return string
     */
    public function buildCreateTable(Schema $schema, Table $table);

    /**
     * build single table drop query
     *
     * @param Schema $schema
     * @param Table $table
     * @return string
     */
    public function buildDropTable(Schema $schema, Table $table);
}
