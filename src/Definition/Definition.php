<?php
namespace PruneMazui\DdlGenerator\Definition;

use PruneMazui\DdlGenerator\DdlGeneratorException;

/**
 * database definition
 *
 * @author ko_tanaka
 */
class Definition
{
    /**
     * @var \PruneMazui\DdlGenerator\Definition\Schema[]
     */
    private $schemas = array();

    /**
     * unset non table schema
     * @return \PruneMazui\DdlGenerator\Definition\Schema
     */
    public function filter()
    {
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
     * @return \PruneMazui\DdlGenerator\Definition\Schema[]
     */
    public function getSchemas()
    {
        ksort($this->schemas);
        return $this->schemas;
    }

    /**
     * Get One Schema
     * @param string $schema_name
     * @return \PruneMazui\DdlGenerator\Definition\Schema|NULL
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
