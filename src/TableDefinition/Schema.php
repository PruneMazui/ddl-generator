<?php
namespace PruneMazui\DdlGenerator\TableDefinition;

class Schema
{
    /**
     * @var string
     */
    private $schema_name;

    /**
     * @var \PruneMazui\DdlGenerator\TableDefinition\Table[]
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
     * ass schema
     * @param Table $table
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\TableDefinition\Schema
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
     * @return \PruneMazui\DdlGenerator\TableDefinition\Table[]
     */
    public function getTables()
    {
        return $this->tables;
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
