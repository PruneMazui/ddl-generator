<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

class Schema
{
    /**
     * @var string
     */
    private $schema_name;

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\Table[]
     */
    private $tables = array();

    public function __construct($schema_name)
    {
        if(is_null($schema_name)) {
            $schema_name = '';
        }
        $this->schema_name = $schema_name;
    }

    /**
     * unset non column table
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Schema
     */
    public function filter()
    {
        foreach($this->tables as $key => $table) {
            $table->filter();
            if($table->countColumns() == 0) {
                // @todo logging
                unset($this->tables[$key]);
            }
        }

        return $this;
    }

    /**
     * @return number
     */
    public function countTables()
    {
        return count($this->tables);
    }

    /**
     * ass schema
     * @param Table $table
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Schema
     */
    public function addTable(Table $table)
    {
        $table_name = $table->getTableName();
        if(array_key_exists($table_name, $this->tables)) {
            throw new DdlGeneratorException("Table '{$table_name}' is already exist in {$this->getSchemaName()}");
        }

        $this->tables[$table_name] = $table;
        return $this;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table[]
     */
    public function getTables()
    {
        ksort($this->tables);
        return $this->tables;
    }

    /**
     * @param string $table_name
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function getTable($table_name)
    {
        if($this->hasTable($table_name)) {
            return $this->tables[$table_name];
        }

        return null;
    }

    /**
     * @param string $table_name
     * @return boolean
     */
    public function hasTable($table_name)
    {
        return array_key_exists($table_name, $this->tables);
    }

    /**
     * @return string
     */
    public function getSchemaName()
    {
        return $this->schema_name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getSchemaName();
    }
}
