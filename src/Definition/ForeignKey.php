<?php
namespace PruneMazui\DdlGenerator\Definition;

class ForeignKey
{
    private $keyName;

    private $schemaName;

    private $tableName;

    private $columnNameList = array();

    private $lockupSchemaName;

    private $lockupTableName;

    private $lockupColumnNameList = array();

    /**
     * @param string $key_name
     * @param string $schema_name
     * @param string $table_name
     * @param string $lockup_schema_name
     * @param string $lockup_table_name
     * @throws DdlGeneratorException
     */
    public function __construct($key_name, $schema_name, $table_name, $lockup_schema_name, $lockup_table_name)
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
            throw new DdlGeneratorException('Lockup Table Name is not allow empty.');
        }
        $this->lockupTableName = $lockup_table_name;
    }

    /**
     *
     * @param string $column_name
     * @param string $lockup_column_name
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\ForeignKey
     */
    public function addColumn($column_name, $lockup_column_name)
    {
        if(! strlen($column_name)) {
            throw new DdlGeneratorException('Column Name is not allow empty.');
        }

        if(! strlen($lockup_column_name)) {
            throw new DdlGeneratorException('Lockup Column Name is not allow empty.');
        }

        $this->columnNameList[] = $column_name;
        $this->lockupColumnNameList[] = $lockup_column_name;
        return $this;
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
    public function getLockupSchemaName()
    {
        return $this->lockupSchemaName;
    }

    /**
     * @return string
     */
    public function getLockupTableName()
    {
        return $this->lockupTableName;
    }

    /**
     * @return array
     */
    public function getLockupColumnNameList()
    {
        return $this->lockupColumnNameList;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getKeyName();
    }
}
