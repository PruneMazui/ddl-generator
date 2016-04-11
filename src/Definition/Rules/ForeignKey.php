<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

use PruneMazui\DdlGenerator\DdlGeneratorException;

class ForeignKey extends AbstractRules
{
    private $keyName;

    private $schemaName;

    private $tableName;

    private $columnNameList = array();

    private $lockupSchemaName;

    private $lockupTableName;

    private $lockupColumnNameList = array();

    private $onUpdate;

    private $onDelete;

    /**
     * @param string $key_name
     * @param string $schema_name
     * @param string $table_name
     * @param string $lockup_schema_name
     * @param string $lockup_table_name
     * @param string $on_update
     * @param string $on_delete
     * @throws DdlGeneratorException
     */
    public function __construct($key_name, $schema_name, $table_name,
            $lockup_schema_name, $lockup_table_name, $on_update, $on_delete)
    {
        if(! strlen($key_name)) {
            throw new DdlGeneratorException('Key Name is not allow empty.');
        }
        $this->keyName = $key_name;

        if(is_null($schema_name)) {
            $schema_name = '';
        }
        $this->schemaName = $schema_name;

        if(! strlen($table_name)) {
            throw new DdlGeneratorException('Table Name is not allow empty.');
        }
        $this->tableName = $table_name;

        if(is_null($lockup_schema_name)) {
            $lockup_schema_name = '';
        }
        $this->lockupSchemaName = $lockup_schema_name;

        if(! strlen($lockup_table_name)) {
            throw new DdlGeneratorException('Lookup Table Name is not allow empty.');
        }
        $this->lockupTableName = $lockup_table_name;

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
     * @param string $lockup_column_name
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Rules\ForeignKey
     */
    public function addColumn($column_name, $lockup_column_name)
    {
        if($this->isLocked) {
            throw new DdlGeneratorException('This object is already immutable.');
        }

        if(! strlen($column_name)) {
            throw new DdlGeneratorException('Column Name is not allow empty.');
        }

        if(! strlen($lockup_column_name)) {
            throw new DdlGeneratorException('Lookup Column Name is not allow empty.');
        }

        $this->columnNameList[] = $column_name;
        $this->lockupColumnNameList[] = $lockup_column_name;
        return $this;
    }

    /**
     * get unique name on definition
     * @return string
     */
    public function getUniqueName()
    {
        $schema_name = $this->schemaName;
        if(strlen($schema_name)) {
            $schema_name .= '.';
        }

        return "{$schema_name}.{$this->tableName}.{$this->keyName}";
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
     * @return string
     */
    public function getSchemaName()
    {
        return $this->schemaName;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getColumnNameList()
    {
        return $this->columnNameList;
    }

    /**
     * @return string
     */
    public function getLookupSchemaName()
    {
        return $this->lockupSchemaName;
    }

    /**
     *
     * @return string
     */
    public function getLookupTableName()
    {
        return $this->lockupTableName;
    }

    /**
     *
     * @return array
     */
    public function getLookupColumnNameList()
    {
        return $this->lockupColumnNameList;
    }

    /**
     *
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
