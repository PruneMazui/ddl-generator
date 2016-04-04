<?php
namespace PruneMazui\DdlGeneratorTest;

use PruneMazui\DdlGenerator\DdlBuilder\MySqlDdlBuilder;
use PruneMazui\DdlGenerator\DataSource\DataSourceInterface;
use PruneMazui\DdlGenerator\Definition\DefinitionFactory;
use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\DataSource\CsvDataSource;

class DdlGenerateTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function flowAllTest()
    {
        $table_datasource = new CsvDataSource(array(
            'filename' => __DIR__ . '/../files/test_table.csv',
            'format'   => 'sjis-win',
        ));

        $index_datasource = new CsvDataSource(array(
            'filename' => __DIR__ . '/../files/test_index.csv',
            'format'   => 'sjis-win',
        ));

        $foreign_key_datasource = new CsvDataSource(array(
            'filename' => __DIR__ . '/../files/test_foreign_key.csv',
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

        if(! $this->hasMySql()) {
            $this->markTestSkipped('MySQL Connection is not defined');
        }

        $db = $this->getMySql();
        $db->exec($content);
    }
}
