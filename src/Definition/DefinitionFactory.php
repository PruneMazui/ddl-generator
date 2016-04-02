<?php
namespace PruneMazui\DdlGenerator\Definition;

use PruneMazui\DdlGenerator\DataSource\DataSourceInterface;
use PruneMazui\DdlGenerator\DataSource\AbstractDataSource as Source;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;
use PruneMazui\DdlGenerator\Definition\Rules\Index;
use PruneMazui\DdlGenerator\Definition\Rules\Column;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;

/**
 * Database Definition Factory
 * @author ko_tanaka
 */
class DefinitionFactory
{
    /**
     * @var DataSourceInterface[]
     */
    private $table_datasources = array();

    /**
     * @var DataSourceInterface[]
     */
    private $foregin_key_datasouces = array();

    /**
     * @var DataSourceInterface[]
     */
    private $index_datasources = array();

    /**
     * @param DataSourceInterface $datasource
     * @return \PruneMazui\DdlGenerator\Definition\DefinitionFactory
     */
    public function addTableDataSource(DataSourceInterface $datasource)
    {
        $datasource->setDataSourceType(Source::TYPE_TABLE);
        $this->table_datasources[] = $datasource;
        return $this;
    }

    /**
     *
     * @param DataSourceInterface $datasource
     * @return \PruneMazui\DdlGenerator\Definition\DefinitionFactory
     */
    public function addForeginKeyDataSource(DataSourceInterface $datasource)
    {
        $datasource->setDataSourceType(Source::TYPE_FOREIGN_KEY);
        $this->foregin_key_datasouces[] = $datasource;
        return $this;
    }

    /**
     * @param DataSourceInterface $datasource
     * @return \PruneMazui\DdlGenerator\Definition\DefinitionFactory
     */
    public function addIndexKeyDataSource(DataSourceInterface $datasource)
    {
        $datasource->setDataSourceType(Source::TYPE_INDEX);
        $this->index_datasources[] = $datasource;
        return $this;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Definition
     */
    public function create()
    {

        // @todo logger interface

        $definition = new Definition();

        // table
        foreach($this->table_datasources as $source) {
            $definition = $this->loadTableSource($source, $definition);
        }

        foreach($this->index_datasources as $source) {
            $definition = $this->loadIndexSource($source, $definition);
        }

        foreach($this->foregin_key_datasouces as $source) {
            $definition = $this->loadForeignKeySource($source, $definition);
        }

        return $definition->finalize();
    }

    /**
     * @param DataSourceInterface $source
     * @return \Closure
     */
    private function createClosureGetFeildData(DataSourceInterface $source)
    {
        return function ($row, $feild) use ($source) {
            $key = $source->getKeyMap($feild);

            if (isset($row[$key])) {
                return $row[$key];
            }
            return null;
        };
    }

    /**
     * @param DataSourceInterface $source
     * @param Definition $definition
     */
    private function loadTableSource(DataSourceInterface $source, Definition $definition)
    {
        $getData = $this->createClosureGetFeildData($source);

        $table = null;
        $schema = null;

        foreach($source->read() as $row) {
            $schema_name = $getData($row, Source::FEILD_SCHEMA_NAME);
            if(is_null($schema) || $schema_name) {
                $schema = $definition->getSchema($schema_name);
                if (is_null($schema)) {
                    $schema = new Schema($schema_name);
                    $definition->addSchema($schema);
                }
            }

            $table_name = $getData($row, Source::FEILD_TABLE_NAME);
            if (strlen($table_name)) {
                $table = $schema->getTable($table_name);
                if(is_null($table)) {
                    $table_comment = $getData($row, Source::FEILD_TABLE_COMMENT);
                    $table = new Table($table_name, $table_comment);
                    $schema->addTable($table);
                }
            }

            if (is_null($table)) {
                continue;
            }

            $column_name = $getData($row, Source::FEILD_COLUMN_NAME);
            if (strlen($column_name)) {
                $data_type = $getData($row, Source::FEILD_COLUMN_DATA_TYPE);
                $required = $getData($row, Source::FEILD_COLUMN_REQUIRED);
                $length = $getData($row, Source::FEILD_COLUMN_LENGTH);
                $default = $getData($row, Source::FEILD_COLUMN_DEFAULT);
                $comment = $getData($row, Source::FEILD_COLUMN_COMMENT);
                $is_auto_increment = $getData($row, Source::FEILD_COLUMN_AUTO_INCREMENT);

                $column = new Column($column_name, $data_type, $required, $length, $default, $comment, $is_auto_increment);
                $table->addColumn($column);

                if ($getData($row, Source::FEILD_COLUMN_PRIMARY_KEY)) {
                    $table->addPrimaryKey($column_name);
                }
            }
        }

        return $definition;
    }

    private function loadIndexSource(DataSourceInterface $source, Definition $definition)
    {
        $getData = $this->createClosureGetFeildData($source);

        $index = null;

        foreach($source->read() as $row) {
            $index_name = $getData($row, Source::FEILD_INDEX_NAME);

            if(strlen($index_name)) {
                $is_unique_index = $getData($row, Source::FEILD_UNIQUE_INDEX);
                $schema_name = $getData($row, Source::FEILD_SCHEMA_NAME);
                $table_name = $getData($row, Source::FEILD_TABLE_NAME);

                $index = new Index($index_name, $is_unique_index, $schema_name, $table_name);
                $definition->addIndex($index);
            }

            if(! $index instanceof Index) {
                continue;
            }

            $column_name = $getData($row, Source::FEILD_COLUMN_NAME);
            if(strlen($column_name)) {
                $index->addColumn($column_name);
            }
        }

        return $definition;
    }

    private function loadForeignKeySource(DataSourceInterface $source, Definition $definition)
    {
        $getData = $this->createClosureGetFeildData($source);

        $foreign_key = null;

        foreach($source->read() as $row) {
            $key_name = $getData($row, Source::FEILD_KEY_NAME);

            if(strlen($key_name)) {
                $schema_name = $getData($row, Source::FEILD_SCHEMA_NAME);
                $table_name = $getData($row, Source::FEILD_TABLE_NAME);
                $lockup_schema_name = $getData($row, Source::FEILD_LOCKUP_SCHEMA_NAME);
                $lockup_table_name = $getData($row, Source::FEILD_LOCKUP_TABLE_NAME);
                $on_update = $getData($row, Source::FEILD_ON_UPDATE);
                $on_delete = $getData($row, Source::FEILD_ON_DELETE);

                $foreign_key = new ForeignKey($key_name, $schema_name, $table_name,
                    $lockup_schema_name, $lockup_table_name, $on_update, $on_delete);

                $definition->addForgienKey($foreign_key);
            }

            if(! $foreign_key instanceof ForeignKey) {
                continue;
            }

            $column_name = $getData($row, Source::FEILD_COLUMN_NAME);
            if(strlen($column_name)) {
                $lockup_column_name = $getData($row, Source::FEILD_LOCKUP_COLUMN_NAME);
                $foreign_key->addColumn($column_name, $lockup_column_name);
            }
        }

        return $definition;
    }


}
