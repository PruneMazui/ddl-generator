<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use Psr\Log\LoggerInterface;

class Schema extends AbstractRules
{
    /**
     * @var string
     */
    private $schemaName;

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\Table[]
     */
    private $tables = [];

    public function __construct($schema_name)
    {
        if(is_null($schema_name)) {
            $schema_name = '';
        }
        $this->schemaName = $schema_name;
    }

    /**
     * unset non column table
     *
     * @param LoggerInterface $logger
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Schema
     */
    public function filter(LoggerInterface $logger)
    {
        foreach($this->tables as $key => $table) {
            if($table->countColumns() == 0) {
                $logger->notice("Table `{$table->getTableName()}` has not column. unset this table.");
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
        if($this->isLocked) {
            throw new DdlGeneratorException('This object is already immutable.');
        }

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
        return $this->schemaName;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getSchemaName();
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\Definition\Rules\AbstractRules::lock()
     */
    public function lock()
    {
        foreach($this->tables as $table) {
            $table->lock();
        }

        return parent::lock();
    }
}
