<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;
use PruneMazui\DdlGenerator\Definition\Rules\Index;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;

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
     * build single table create query
     *
     * @param Schema $schema
     * @param Table $table
     * @return string
     */
    public function buildCreateTable(Schema $schema, Table $table);

    /**
     * build all  drop table query
     *
     * @param Definition $definition
     * @return string
     */
    public function buildAllDropTable(Definition $definition);

    /**
     * build single table drop query
     *
     * @param Schema $schema
     * @param Table $table
     * @return string
     */
    public function buildDropTable(Schema $schema, Table $table);

    /**
     * build all create index
     * @param Definition $definition
     * @return string
     */
    public function buildAllCreateIndex(Definition $definition);

    /**
     * build single create index
     * @param Index $index
     * @return string
     */
    public function buildCreateIndex(Index $index);

    /**
     * build all create foreign key
     * @param Definition $definition
     * @return string
     */
    public function buildAllCreateForeignKey(Definition $definition);

    /**
     * build single create foreign key
     * @param ForeignKey $foreign_key
     * @return string
     */
    public function buildCreateForeignKey(ForeignKey $foreign_key);
}
