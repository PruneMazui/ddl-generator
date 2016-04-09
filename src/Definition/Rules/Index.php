<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

use PruneMazui\DdlGenerator\DdlGeneratorException;

class Index
{
    private $keyName;

    private $isUniqueIndex;

    private $schemaName;

    private $tableName;

    private $columnNameList = array();

    /**
     * @param string $key_name
     * @param bool $is_unique_index
     * @param string $schema_name
     * @param string $table_name
     */
    public function __construct($key_name, $is_unique_index, $schema_name, $table_name)
    {
        if(! strlen($key_name)) {
            throw new DdlGeneratorException('Key Name is not allow empty.');
        }
        $this->keyName = $key_name;

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
     * @return string
     */
    public function getUniqueName()
    {
        $schema_name = $this->schemaName;
        if(strlen($schema_name)) {
            $schema_name .= '.';
        }

        return "{$schema_name}.{$this->tableName}.{$this->keyName}";
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
    public function getKeyName()
    {
        return $this->keyName;
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
        return (string) $this->getKeyName();
    }
}
