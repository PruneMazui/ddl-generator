<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

use PruneMazui\DdlGenerator\DdlGeneratorException;

class ForeignKey
{
    private $table;

    private $lookupTable;

    private $keyName;

    private $columnNameList = array();

    private $lookupColumnNameList = array();

    private $onUpdate;

    private $onDelete;

    /**
     * @param Table $lookup_table
     * @param string $key_name
     * @param string $on_update
     * @param string $on_delete
     * @throws DdlGeneratorException
     */
    public function __construct(Table $table, Table $lookup_table, $key_name, $on_update, $on_delete)
    {
        $this->table = $table;
        $this->lookupTable = $lookup_table;

        if(! strlen($key_name)) {
            throw new DdlGeneratorException('Key Name is not allow empty.');
        }
        $this->keyName = $key_name;

        if(! strlen($on_update)) {
            throw new DdlGeneratorException('On Update is not allow empty.');
        }
        $this->onUpdate = $on_update;

        if(! strlen($on_delete)) {
            throw new DdlGeneratorException('On Delete is not allow empty.');
        }
        $this->onDelete = $on_delete;
    }

    /**
     *
     * @param string $column_name
     * @param string $lookup_column_name
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Rules\ForeignKey
     */
    public function addColumn($column_name, $lookup_column_name)
    {
        if(! strlen($column_name)) {
            throw new DdlGeneratorException('Column Name is not allow empty.');
        }

        if(! strlen($lookup_column_name)) {
            throw new DdlGeneratorException('Lookup Column Name is not allow empty.');
        }

        $this->columnNameList[] = $column_name;
        $this->lookupColumnNameList[] = $lookup_column_name;
        return $this;
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
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getColumnNameList()
    {
        return $this->columnNameList;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function getLookupTable()
    {
        return $this->lookupTable;
    }
    /**
     * @return array
     */
    public function getLookupColumnNameList()
    {
        return $this->lookupColumnNameList;
    }

    /**
     * @return string
     */
    public function getOnUpdate()
    {
        return $this->onUpdate;
    }

    /**
     * @return string
     */
    public function getOnDelete()
    {
        return $this->onDelete;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getKeyName();
    }
}
