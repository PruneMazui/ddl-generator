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

        $key_names = array();

        // unset non column index
        foreach($this->indexes as $key => $index) {
            if($index->countColumns() == 0) {
                // @todo log notice
                unset($this->indexes[$key]);
            }

            // check conflict key name
            $unique_key_name = $this->buildKeyName(
                $index->getIndexName(),
                $index->getSchemaName(),
                $index->getTableName()
            );

            if(array_key_exists($unique_key_name, $key_names)) {
                throw new DdlGeneratorException('key name conflict : ' . $unique_key_name);
            }
            $key_names[$unique_key_name] = $unique_key_name;

            // check has column
            if(! $this->hasSchema($index->getSchemaName())) {
                throw new DdlGeneratorException("Schema is not found for index in Schema `{$index->getSchemaName()}`");
            }
            $schema = $this->getSchema($index->getSchemaName());

            if(! $schema->hasTable($index->getTableName())) {
                throw new DdlGeneratorException("Table is not found for index in Schema `{$index->getSchemaName()}` Table`{$index->getTableName()}`");
            }
            $table = $schema->getTable($index->getTableName());

            foreach ($index->getColumnNameList() as $column_name) {
                if(! $table->hasColumn($column_name)) {
                    throw new DdlGeneratorException("Column is not found for index in Schema`{$index->getSchemaName()}` Table`{$index->getTableName()}` Column`{$column_name}`");
                }
            }
        }

        // unset non column foreign key
        foreach($this->foreignKeys as $key => $foreign_key) {
            if($foreign_key->countColumns() == 0) {
                // @todo log notice
                unset($this->foreignKeys[$key]);
            }

            // check conflict key name
            $unique_key_name = $this->buildKeyName(
                $foreign_key->getKeyName(),
                $foreign_key->getSchemaName(),
                $foreign_key->getTableName()
            );

            if(array_key_exists($unique_key_name, $key_names)) {
                throw new DdlGeneratorException('key name conflict : ' . $unique_key_name);
            }
            $key_names[$unique_key_name] = $unique_key_name;

            // check has column
            if(! $this->hasSchema($foreign_key->getSchemaName())) {
                throw new DdlGeneratorException("Schema is not found for index in Schema `{$foreign_key->getSchemaName()}`");
            }
            $schema = $this->getSchema($foreign_key->getSchemaName());

            if(! $schema->hasTable($foreign_key->getTableName())) {
                throw new DdlGeneratorException("Table is not found for index in Schema `{$foreign_key->getSchemaName()}` Table`{$index->getTableName()}`");
            }
            $table = $schema->getTable($foreign_key->getTableName());

            // lockup
            if(! $this->hasSchema($foreign_key->getLockupSchemaName())) {
                throw new DdlGeneratorException("Schema is not found for index in Schema `{$foreign_key->getLockupSchemaName()}`");
            }
            $lockup_schema = $this->getSchema($foreign_key->getLockupSchemaName());

            if(! $lockup_schema->hasTable($foreign_key->getLockupTableName())) {
                throw new DdlGeneratorException("Table is not found for index in Schema `{$foreign_key->getLockupSchemaName()}` Table`{$index->getLockupTableName()}`");
            }
            $lockup_table = $lockup_schema->getTable($foreign_key->getTableName());

            $column_name_list = $foreign_key->getColumnNameList();
            $lockup_column_name_list = $foreign_key->getLockupColumnNameList();

            foreach ($column_name_list as $key => $column_name) {
                $lockup_column_name = $lockup_column_name_list[$key];

                if(! $table->hasColumn($column_name)) {
                    throw new DdlGeneratorException("Column is not found for index in Schema`{$foreign_key->getSchemaName()}` Table`{$foreign_key->getTableName()}` Column`{$column_name}`");
                }

                if(! $lockup_table->hasColumn($lockup_column_name)) {
                    throw new DdlGeneratorException("Column is not found for index in Schema`{$foreign_key->getLockupSchemaName()}` Table`{$foreign_key->getLockupTableName()}` Column`{$lockup_column_name}`");
                }

                // @todo log column dataType is not matched
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
    public function countIndexes()
    {
        return count($this->indexes);
    }

    /**
     * @return number
     */
    public function countForeignKeys()
    {
        return count($this->foreignKeys);
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
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Index[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\ForeignKey[]
     */
    public function getForeignKeys()
    {
        return $this->foreignKeys;
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
