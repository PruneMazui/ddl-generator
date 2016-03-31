<?php
namespace PruneMazui\DdlGeneratorTest;

use PruneMazui\DdlGenerator\DataSource\ExcelDataSource;
use PruneMazui\DdlGenerator\DdlBuilder\MySqlDdlBuilder;
use PruneMazui\DdlGenerator\DataSource\DataSourceInterface;
use PruneMazui\DdlGenerator\Definition\DefinitionFactory;
use PruneMazui\DdlGenerator\Definition\Definition;

class DdlGenerateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function flowAllTest()
    {
        $datasource = new ExcelDataSource(array(
            'filename' => __DIR__ . '/../files/db_specifications.xlsx',
            'sheets'   => 'テーブル定義',
        ));

        assertTrue($datasource instanceof DataSourceInterface);

        $factory = new DefinitionFactory();
        $definition = $factory->addTableDataSource($datasource)->create();

        assertTrue($definition instanceof Definition);

        $builder = new MySqlDdlBuilder();
        $content = $builder->buildAll($definition);

        assertTrue(is_string($content));
        assertNotEmpty($content);
    }
}
