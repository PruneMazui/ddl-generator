<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

use PruneMazui\DdlGenerator\DdlGeneratorException;

class Index
{
    private $indexName;

    private $isUniqueIndex;

    private $schemaName;

    private $tableName;

    private $columnNameList = array();

    /**
     * @param string $index_name
     * @param bool $is_unique_index
     * @param string $schema_name
     * @param string $table_name
     */
    public function __construct($index_name, $is_unique_index, $schema_name, $table_name)
    {
        if(! strlen($index_name)) {
            throw new DdlGeneratorException('Index Name is not allow empty.');
        }
        $this->indexName = $index_name;

        $this->isUniqueIndex = !! $is_unique_index;

        if(is_null($schema_name)) {
            $schema_name = '';
        }
        $this->schemaName = $schema_name;

        if(! strlen($table_name)) {
            throw new DdlGeneratorException('Table Name is not allow empty.');
        }
        $this->tableName = $table_name;
    }

    /**
     * count column
     * @return number
     */
    public function countColumns()
    {
        return count($this->columnNameList);
    }

    /**
     * add column
     * @param string $column_name
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Index
     */
    public function addColumn($column_name)
    {
        if(! strlen($column_name)) {
            throw new DdlGeneratorException('Column Name is not allow empty.');
        }

        $this->columnNameList[] = $column_name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUniqueIndex()
    {
        return $this->isUniqueIndex;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @return string
     */
    public function getSchemaName()
    {
        return $this->schemaName;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getColumnNameList()
    {
        return $this->columnNameList;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getIndexName();
    }
}
