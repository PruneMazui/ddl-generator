<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

use PruneMazui\DdlGenerator\DdlGeneratorException;

class Index
{
    private $table;

    private $keyName;

    private $isUniqueIndex;

    private $columnNameList = array();

    /**
     * @param string $key_name
     * @param bool $is_unique_index
     * @param string $schema_name
     * @param string $table_name
     */
    public function __construct(Table $table, $key_name, $is_unique_index)
    {
        $this->table = $table;

        if(! strlen($key_name)) {
            throw new DdlGeneratorException('Index Name is not allow empty.');
        }
        $this->keyName = $key_name;

        $this->isUniqueIndex = !! $is_unique_index;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function getTable()
    {
        return $this->table;
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
    public function getKeyName()
    {
        return $this->keyName;
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
