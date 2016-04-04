<?php
namespace PruneMazui\DdlGenerator\Definition;

use PruneMazui\DdlGenerator\DataSource\DataSourceInterface;
use PruneMazui\DdlGenerator\DataSource\AbstractDataSource as Source;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;
use PruneMazui\DdlGenerator\Definition\Rules\Index;
use PruneMazui\DdlGenerator\Definition\Rules\Column;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;
use PruneMazui\DdlGenerator\DataSource\Feild;
use PruneMazui\DdlGenerator\DdlGeneratorException;

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
     * @param Definition $definition
     */
    private function loadTableSource(DataSourceInterface $source, Definition $definition)
    {
        $table = null;
        $schema = null;

        foreach($source->read() as $row) {
            $schema_name = $row->getFeild(Feild::SCHEMA_NAME);
            if(is_null($schema) || $schema_name) {
                $schema = $definition->getSchema($schema_name);
                if (is_null($schema)) {
                    $schema = new Schema($schema_name);
                    $definition->addSchema($schema);
                }
            }

            $table_name = $row->getFeild(Feild::TABLE_NAME);
            if (strlen($table_name)) {
                $table = $schema->getTable($table_name);
                if(is_null($table)) {
                    $table_comment = $row->getFeild(Feild::TABLE_COMMENT);
                    $table = new Table($table_name, $table_comment);
                    $schema->addTable($table);
                }
            }

            if (is_null($table)) {
                continue;
            }

            $column_name = $row->getFeild(Feild::COLUMN_NAME);
            if (strlen($column_name)) {
                $data_type = $row->getFeild(Feild::COLUMN_DATA_TYPE);
                $required = $row->getFeild(Feild::COLUMN_REQUIRED);
                $length = $row->getFeild(Feild::COLUMN_LENGTH);
                $default = $row->getFeild(Feild::COLUMN_DEFAULT);
                $comment = $row->getFeild(Feild::COLUMN_COMMENT);
                $is_auto_increment = $row->getFeild(Feild::COLUMN_AUTO_INCREMENT);

                $column = new Column($column_name, $data_type, $required, $length, $default, $comment, $is_auto_increment);
                $table->addColumn($column);

                if ($row->getFeild(Feild::COLUMN_PRIMARY_KEY)) {
                    $table->addPrimaryKey($column_name);
                }
            }
        }

        return $definition;
    }

    private function loadIndexSource(DataSourceInterface $source, Definition $definition)
    {
        $index = null;

        foreach($source->read() as $row) {
            $key_name = $row->getFeild(Feild::KEY_NAME);

            if(strlen($key_name)) {
                $is_unique_index = $row->getFeild(Feild::UNIQUE_INDEX);

                $schema_name = $row->getFeild(Feild::SCHEMA_NAME);
                $schema = $definition->getSchema($schema_name);
                if(is_null($schema)) {
                    throw new DdlGeneratorException("Schema `{$schema_name}` is not found.");
                }

                $table_name = $row->getFeild(Feild::TABLE_NAME);
                $table = $schema->getTable($table_name);
                if(is_null($table)) {
                    throw new DdlGeneratorException("Table `{$table_name}` in Schema `{$schema_name}` is not found.");
                }

                $index = new Index($table, $key_name, $is_unique_index);
                $table->addIndex($index);
            }

            if(! $index instanceof Index) {
                continue;
            }

            $column_name = $row->getFeild(Feild::COLUMN_NAME);
            if(strlen($column_name)) {
                $index->addColumn($column_name);
            }
        }

        return $definition;
    }

    private function loadForeignKeySource(DataSourceInterface $source, Definition $definition)
    {
        $foreign_key = null;

        foreach($source->read() as $row) {
            $key_name = $row->getFeild(Feild::KEY_NAME);

            if(strlen($key_name)) {
                $schema_name = $row->getFeild(Feild::SCHEMA_NAME);
                $schema = $definition->getSchema($schema_name);
                if(is_null($schema)) {
                    throw new DdlGeneratorException("Schema `{$schema_name}` is not found.");
                }

                $table_name = $row->getFeild(Feild::TABLE_NAME);
                $table = $schema->getTable($table_name);
                if(is_null($table)) {
                    throw new DdlGeneratorException("Table `{$table_name}` in Schema `{$schema_name}` is not found.");
                }

                $lookup_schema_name = $row->getFeild(Feild::LOOKUP_SCHEMA_NAME);
                $lookup_schema = $definition->getSchema($lookup_schema_name);
                if(is_null($lookup_schema)) {
                    throw new DdlGeneratorException("Schema `{$lookup_schema_name}` is not found.");
                }

                $lookup_table_name = $row->getFeild(Feild::LOOKUP_TABLE_NAME);
                $lookup_table = $schema->getTable($lookup_table_name);
                if(is_null($lookup_table)) {
                    throw new DdlGeneratorException("Table `{$lookup_table_name}` in Schema `{$lookup_schema_name}` is not found.");
                }

                $on_update = $row->getFeild(Feild::ON_UPDATE);
                $on_delete = $row->getFeild(Feild::ON_DELETE);

                $foreign_key = new ForeignKey($table, $lookup_table, $key_name, $on_update, $on_delete);

                $table->addForgienKey($foreign_key);
            }

            if(! $foreign_key instanceof ForeignKey) {
                continue;
            }

            $column_name = $row->getFeild(Feild::COLUMN_NAME);
            if(strlen($column_name)) {
                $lookup_column_name = $row->getFeild(Feild::LOOKUP_COLUMN_NAME);
                $foreign_key->addColumn($column_name, $lookup_column_name);
            }
        }

        return $definition;
    }


}
