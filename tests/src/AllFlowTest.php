<?php
namespace PruneMazui\DdlGeneratorTest;

use PruneMazui\DdlGenerator\DdlBuilder\MySqlDdlBuilder;
use PruneMazui\DdlGenerator\DataSource\DataSourceInterface;
use PruneMazui\DdlGenerator\Definition\DefinitionFactory;
use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\DataSource\CsvDataSource;

class AllFlowTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function allFlowTest()
    {
        $table_datasource = new CsvDataSource(array(
            'filename' => __DIR__ . '/files/test_table.csv',
            'format'   => 'sjis-win',
        ));

        $index_datasource = new CsvDataSource(array(
            'filename' => __DIR__ . '/files/test_index.csv',
            'format'   => 'sjis-win',
        ));

        $foreign_key_datasource = new CsvDataSource(array(
            'filename' => __DIR__ . '/files/test_foreign_key.csv',
            'format'   => 'sjis-win',
        ));

        assertTrue($table_datasource instanceof DataSourceInterface);
        assertTrue($index_datasource instanceof DataSourceInterface);
        assertTrue($foreign_key_datasource instanceof DataSourceInterface);

        $factory = new DefinitionFactory();
        $definition = $factory
            ->addTableDataSource($table_datasource)
            ->addIndexKeyDataSource($index_datasource)
            ->addForeginKeyDataSource($foreign_key_datasource)
            ->create()
        ;

        assertTrue($definition instanceof Definition);

        $builder = new MySqlDdlBuilder();
        $content = $builder->buildAll($definition);

        assertTrue(is_string($content));
        assertNotEmpty($content);

        assertContains('DROP TABLE', $content);
        assertContains('CREATE TABLE', $content);
        assertContains('CREATE INDEX', $content);
        assertContains('ALTER TABLE', $content);

        // encode
        $builder->setConfig(array(
            'format' => 'SJIS-win',
        ));
        $content_sjis = $builder->buildAll($definition);
        assertEquals($content, mb_convert_encoding($content_sjis, 'UTF-8', 'SJIS-win'));

        if(! $this->hasMySql()) {
            $this->markTestSkipped('MySQL Connection is not defined');
        }

        $db = $this->getMySql();

        // drop non table exists
        $builder->setConfig(array());
        $db->getConnection()->exec($builder->buildAllDropTable($definition));
        assertCount(0, $db->fetchColmun("SHOW TABLES;"));

        // mysql is no schema support
        $schema = $definition->getSchema("");

        // create table
        $db->getConnection()->exec($builder->buildAllCreateTable($definition));
        $tables = $db->fetchColmun("SHOW TABLES;");
        assertCount($schema->countTables(), $tables);

        // exists all table
        foreach($schema->getTables() as $table) {
            assertContains($table->getTableName(), $tables);

            $columns = $db->fetchColmun("SHOW COLUMNS FROM {$table->getTableName()}");

            foreach($table->getColumns() as $column) {
                assertContains($column->getColumnName(), $columns);
            }
        }
    }
}
