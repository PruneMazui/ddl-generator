<?php
namespace PruneMazui\DdlGenerator\TableDefinition;

use PruneMazui\DdlGenerator\DdlGeneratorException;

class TableDefinition
{
    /**
     * @var \PruneMazui\DdlGenerator\TableDefinition\Schema[]
     */
    private $schemas = array();

    /**
     * @return \PruneMazui\DdlGenerator\TableDefinition\Schema[]
     */
    public function getSchemas()
    {
        return $this->schemas;
    }

    /**
     * add schema
     * @param Schema $schema
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\TableDefinition\TableDefinition
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
