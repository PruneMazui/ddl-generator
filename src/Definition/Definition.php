<?php
namespace PruneMazui\DdlGenerator\Definition;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;
use PruneMazui\DdlGenerator\Definition\Rules\Index;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;

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

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\ForeignKey[]
     */
    private $foreignKeys = array();

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\Index[]
     */
    private $indexes = array();

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

    /**
     * add index
     * @param Index $index
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Definition
     */
    public function addIndex(Index $index)
    {
        $index_name = $index->getIndexName();
        if(array_key_exists($index_name, $this->indexes)) {
            throw new DdlGeneratorException("Index '{$index_name}' is already exist");
        }

        $this->indexes[$index_name] = $index;
        return $this;
    }

    /**
     * add foreign key
     * @param ForeignKey $foreign_key
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Definition
     */
    public function addForgienKey(ForeignKey $foreign_key)
    {
        $key_name = $foreign_key->getKeyName();
        if(array_key_exists($key_name, $this->foreignKeys)) {
            throw new DdlGeneratorException("Foreign Key '{$key_name}' is already exist");
        }

        $this->foreignKeys[$key_name] = $foreign_key;
        return $this;
    }
}
