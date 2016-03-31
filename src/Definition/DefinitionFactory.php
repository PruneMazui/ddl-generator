<?php
namespace PruneMazui\DdlGenerator\Definition;

use PruneMazui\DdlGenerator\DataSource\DataSourceInterface;
use PruneMazui\DdlGenerator\DataSource\AbstractDataSource as Source;

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
        $definition = new Definition();

        // table
        foreach($this->table_datasources as $source) {
            $definition = $this->loadTableSource($source, $definition);
        }

        $definition->filter();

        foreach($this->index_datasources as $source) {
            $definition = $this->loadIndexSource($source, $definition);
        }

        foreach($this->foregin_key_datasouces as $source) {
            $definition = $this->loadForeignKeySource($source, $definition);
        }

        return $definition;
    }

    /**
     * @param DataSourceInterface $source
     * @param Definition $definition
     */
    private function loadTableSource(DataSourceInterface $source, Definition $definition)
    {
        $getData = function ($row, $feild) use ($source) {
            $key = $source->getKeyMap($feild);

            if (isset($row[$key])) {
                return $row[$key];
            }
            return null;
        };

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
        // @todo index
        return $definition;
    }

    private function loadForeignKeySource(DataSourceInterface $source, Definition $definition)
    {
        // @todo foreign key
        return $definition;
    }
}
