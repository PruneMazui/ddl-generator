<?php
namespace PruneMazui\DdlGeneratorTest;

use PruneMazui\DdlGenerator\DataSource\ExcelDataSource;
use \PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\DefinitionFactory;
use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\DataSource\RowData;
use PruneMazui\DdlGenerator\DataSource\Feild;
use PruneMazui\DdlGenerator\DataSource\CsvDataSource;

class ExcelDataSourceTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function errorTest()
    {
        try {
            $table_datasource = new ExcelDataSource();
            $table_datasource->read();

            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $table_datasource = new ExcelDataSource(array(
                'filename' => __DIR__ . '/files/db_specifications.xlsx',
            ));
            $table_datasource->read();

            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $table_datasource = new ExcelDataSource();
            $table_datasource->setDataSourceType('hoge');

            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $table_datasource = new CsvDataSource();
            $table_datasource->read();

            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $table_datasource = new CsvDataSource(array(
                'filename' => __DIR__ . '/files',
            ));
            $table_datasource->read();

            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $table_datasource = new CsvDataSource(array(
                'filename' => __DIR__ . '/files/nofile',
            ));
            $table_datasource->read();

            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @test
     */
    public function excelTest()
    {
        $table_datasource = new ExcelDataSource(array(
            'filename' => __DIR__ . '/files/db_specifications.xlsx',
            'sheets'   => array('テーブル定義', 'テーブル定義2'),
        ));

        $factory = new DefinitionFactory();

        $content = $table_datasource->read();
        assertNotEmpty($content);

        $row = reset($content);
        assertTrue($row instanceof RowData);

        assertNotNull($row->getFeild(Feild::TABLE_NAME));
        assertNull($row->getFeild(Feild::KEY_NAME));

        // Array Access
        assertTrue(isset($row[Feild::TABLE_NAME]));
        assertEquals($row[Feild::TABLE_NAME], $row->getFeild(Feild::TABLE_NAME));
        try {
            $row[Feild::TABLE_NAME] = "hoge";
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            unset($row[Feild::TABLE_NAME]);
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        $definition = $factory->addTableDataSource($table_datasource)->create();

        assertTrue($definition instanceof Definition);
        assertTrue($definition->hasTable("", "t_user"));
        assertTrue($definition->hasTable("", "t_test"));
    }
}
