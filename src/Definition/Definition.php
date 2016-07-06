<?php
namespace PruneMazui\DdlGenerator\Definition;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;
use PruneMazui\DdlGenerator\Definition\Rules\Index;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
/**
 * database definition
 *
 * @todo key's relation is Schema
 *
 * @author ko_tanaka
 */
class Definition
{
    private $isLocked = false;

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\Schema[]
     */
    private $schemas = [];

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\ForeignKey[]
     */
    private $foreignKeys = [];

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\Index[]
     */
    private $indexes = [];

    /**
     * execute filtering and checking to all tables
     *
     * @param LoggerInterface optional $logger
     * @param bool optional $lock
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Schema
     */
    public function finalize(LoggerInterface $logger = null, $lock = true)
    {
        if(is_null($logger)) {
            $logger = new NullLogger();
        }

        // unset non table schema
        foreach($this->schemas as $key => $schema) {
            $schema->filter($logger);
            if($schema->countTables() == 0) {
                $logger->notice("Schema `{$schema->getSchemaName()}` has not column. unset this Schema.");
                unset($this->schemas[$key]);
            }
        }

        // unset non column index
        foreach($this->indexes as $key => $index) {
            if($index->countColumns() == 0) {
                $logger->notice("Index `{$index->getUniqueName()}` has no column. unset this index.");
                unset($this->indexes[$key]);
                continue;
            }

            foreach ($index->getColumnNameList() as $column_name) {
                if(! $this->hasColumn($index->getSchemaName(), $index->getTableName(), $column_name)) {
                    $name = $index->getSchemaName();
                    $name .= strlen($name) ? '.' : '';
                    $name .= $index->getTableName(). '.' . $column_name;
                    throw new DdlGeneratorException("Column `{$name}` is not found for index.");
                }
            }
        }

        // unset non column foreign key
        foreach($this->foreignKeys as $key => $foreign_key) {
            if($foreign_key->countColumns() == 0) {
                $logger->notice("Foreign Key `{$foreign_key->getUniqueName()}` has no column. unset this foreign key.");
                unset($this->foreignKeys[$key]);
                continue;
            }

            $column_list = $foreign_key->getColumnNameList();
            $lookup_column_list = $foreign_key->getLookupColumnNameList();

            if(count($column_list) !== count($lookup_column_list)) {
                throw new DdlGeneratorException("Column count and lookup column count is not match."); // @codeCoverageIgnore
            }

            foreach($column_list as $idx => $column_name) {
                $lookup_column_name = $lookup_column_list[$idx];

                $column = $this->getColumn($foreign_key->getSchemaName(), $foreign_key->getTableName(), $column_name);
                $lookup_column = $this->getColumn($foreign_key->getLookupSchemaName(), $foreign_key->getLookupTableName(), $lookup_column_name);

                if(is_null($column)) {
                    $name = $foreign_key->getSchemaName();
                    $name .= strlen($name) ? '.' : '';
                    $name .= $foreign_key->getTableName(). '.' . $column_name;
                    throw new DdlGeneratorException("Column `{$name}` is not found for foreign key.");
                }

                if(is_null($lookup_column)) {
                    $name = $foreign_key->getLookupSchemaName();
                    $name .= strlen($name) ? '.' : '';
                    $name .= $foreign_key->getLookupTableName() . "." .  $lookup_column_name;
                    throw new DdlGeneratorException("Lookup Column `{$name}` is not found for foreign key.");
                }

                if($column->getDataType() != $lookup_column->getDataType()) {
                    $logger->notice("Foreign Key `{$foreign_key->getUniqueName()}` column data type is not equal to lookup column data type.");
                }
            }
        }

        if($lock) {
            $this->lock();
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
        ksort($this->indexes);
        return $this->indexes;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\ForeignKey[]
     */
    public function getForeignKeys()
    {
        ksort($this->foreignKeys);
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
        if($this->isLocked) {
            throw new DdlGeneratorException('This object is already immutable.');
        }

        $schema_name = $schema->getSchemaName();
        if(array_key_exists($schema_name, $this->schemas)) {
            throw new DdlGeneratorException("Schema '{$schema_name}' is already exist");
        }

        $this->schemas[$schema_name] = $schema;
        return $this;
    }

    /**
     * get table
     * @param string $schema_name
     * @param string $table_name
     * @return NULL|\PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function getTable($schema_name, $table_name)
    {
        $schema = $this->getSchema($schema_name);
        if(! $schema instanceof Schema) {
            return null;
        }

        return $schema->getTable($table_name);
    }

    /**
     * has table
     * @param string $schema_name
     * @param string $table_name
     * @return boolean
     */
    public function hasTable($schema_name, $table_name)
    {
        return ! is_null($this->getTable($schema_name, $table_name));
    }

    /**
     * get column
     *
     * @param string $schema_name
     * @param string $table_name
     * @param string $column_name
     * @return string|\PruneMazui\DdlGenerator\Definition\Rules\Column
     */
    public function getColumn($schema_name, $table_name, $column_name)
    {
        $table = $this->getTable($schema_name, $table_name);
        if(! $table instanceof Table) {
            return null;
        }

        return $table->getColumn($column_name);
    }

    /**
     * has column
     *
     * @param string $schema_name
     * @param string $table_name
     * @param string $column_name
     * @return boolean
     */
    public function hasColumn($schema_name, $table_name, $column_name)
    {
        return ! is_null($this->getColumn($schema_name, $table_name, $column_name));
    }

    /**
     * add index
     * @param Index $index
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Definition
     */
    public function addIndex(Index $index)
    {
        if($this->isLocked) {
            throw new DdlGeneratorException('This object is already immutable.');
        }

        $key_name = $index->getUniqueName();
        if(array_key_exists($key_name, $this->indexes)) {
            throw new DdlGeneratorException("Index Key '{$key_name}' is already exist");
        }

        if(array_key_exists($key_name, $this->foreignKeys)) {
            throw new DdlGeneratorException("Foreign Key '{$key_name}' is already exist");
        }

        $this->indexes[$key_name] = $index;
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
        if($this->isLocked) {
            throw new DdlGeneratorException('This object is already immutable.');
        }

        $key_name = $foreign_key->getUniqueName();
        if(array_key_exists($key_name, $this->indexes)) {
            throw new DdlGeneratorException("Index Key '{$key_name}' is already exist");
        }

        if(array_key_exists($key_name, $this->foreignKeys)) {
            throw new DdlGeneratorException("Foreign Key '{$key_name}' is already exist");
        }

        $this->foreignKeys[$key_name] = $foreign_key;
        return $this;
    }

    /**
     *
     * @return \PruneMazui\DdlGenerator\Definition\Definition
     */
    public function lock()
    {
        foreach($this->schemas as $schema) {
            $schema->lock();
        }

        $this->isLocked = true;

        return $this;
    }
}
