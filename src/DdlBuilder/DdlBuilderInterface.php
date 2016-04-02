<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;

/**
 * interface for DDL Builder
 * @author ko_tanaka
 */
interface DdlBuilderInterface
{
    /**
     * build all query
     *
     * @param Definition $definition
     * @param boolean optional $add_drop_table
     * @return string
     */
    public function buildAll(Definition $definition, $add_drop_table = true);

    /**
     * build all create table query
     *
     * @param Definition $definition
     * @return string
     */
    public function buildAllCreateTable(Definition $definition);

    /**
     * build all  drop table query
     *
     * @param Definition $definition
     * @return string
     */
    public function buildAllDropTable(Definition $definition);

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
