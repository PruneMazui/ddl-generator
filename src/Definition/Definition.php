<?php
namespace PruneMazui\DdlGenerator\Definition;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;
/**
 * database definition
 *
 * @author ko_tanaka
 */
class Definition
{
    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\Schema[]
     */
    private $schemas = array();

    private function buildKeyName($key_name, $schema_name, $table_name)
    {
        if(strlen($schema_name)) {
            $schema_name = $schema_name . '.';
        }

        return "{$schema_name}{$table_name}.{$key_name}";
    }

    /**
     * execute filtering and checking to all tables
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Schema
     */
    public function finalize()
    {
        // unset non table schema
        foreach($this->schemas as $key => $schema) {
            $schema->filter();
            if($schema->countTables() == 0) {
                unset($this->schemas[$key]);
            }
        }

        return $this;
    }

    /**
     * @return number
     */
    public function countSchemas()
    {
        return count($this->schemas);
    }

    /**
     * @return number
     */
    public function countAllTables()
    {
        $count = 0;

        foreach($this->schemas as $schema) {
            $count += $schema->countTables();
        }

        return $count;
    }

    /**
     * @return number
     */
    public function countAllColumns()
    {
        $count = 0;

        foreach($this->schemas as $schema) {
            foreach($schema->getTables() as $table) {
                $count += $table->countColumns();
            }
        }

        return $count;
    }

    /**
     * @return number
     */
    public function countAllIndexes()
    {
        $count = 0;

        foreach($this->schemas as $schema) {
            foreach($schema->getTables() as $table) {
                $count += $table->countIndexes();
            }
        }

        return $count;
    }

    /**
     * @return number
     */
    public function countAllForeignKeys()
    {
        $count = 0;

        foreach($this->schemas as $schema) {
            foreach($schema->getTables() as $table) {
                $count += $table->countForeignKeys();
            }
        }

        return $count;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Schema[]
     */
    public function getSchemas()
    {
        ksort($this->schemas);
        return $this->schemas;
    }

    /**
     * Get One Schema
     * @param string $schema_name
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Schema|NULL
     */
    public function getSchema($schema_name)
    {
        if(is_null($schema_name)) {
            $schema_name = '';
        }

        if($this->hasSchema($schema_name)) {
            return $this->schemas[$schema_name];
        }

        return null;
    }

    /**
     * @param string $schema_name
     * @return boolean
     */
    public function hasSchema($schema_name)
    {
        if(is_null($schema_name)) {
            $schema_name = '';
        }

        return array_key_exists($schema_name, $this->schemas);
    }

    /**
     * add schema
     * @param Schema $schema
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Definition
     */
    public function addSchema(Schema $schema)
    {
        $schema_name = $schema->getSchemaName();
        if(array_key_exists($schema_name, $this->schemas)) {
            throw new DdlGeneratorException("Schema '{$schema_name}' is already exist");
        }

        $this->schemas[$schema_name] = $schema;
        return $this;
    }
}
