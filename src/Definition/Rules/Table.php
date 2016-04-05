<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

use PruneMazui\DdlGenerator\DdlGeneratorException;

class Table
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $comment = '';

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\Column[]
     */
    private $columns = array();

    /**
     * @var array
     */
    private $primary_key = array();

    /**
     * @return number
     */
    public function countColumns()
    {
        return count($this->columns);
    }

    /**
     * @param string $table_name
     * @param string optional $comment
     */
    public function __construct($table_name, $comment = null)
    {
        if(! strlen($table_name)) {
            throw new DdlGeneratorException('Table name is not allow empty.');
        }

        $this->tableName = $table_name;

        if (!is_null($comment)) {
            $this->comment = $comment;
        }
    }

    /**
     * get table name
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * get table comment
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $table_name
     * @return boolean
     */
    public function hasColumn($column_name)
    {
        return array_key_exists($column_name, $this->columns);
    }

    /**
     * @param string $table_name
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Column
     */
    public function getColumn($column_name)
    {
        if($this->hasColumn($column_name)) {
            return $this->columns[$column_name];
        }

        return null;
    }

    /**
     * set primary key
     * @param string | array $primary_key
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function setPrimaryKey($primary_key)
    {
        if(is_string($primary_key)) {
            $primary_key = array($primary_key);
        }

        foreach($primary_key as $column_name) {
            if (! array_key_exists($column_name, $this->columns)) {
                throw new DdlGeneratorException("Column '{$column_name}' is not found in {$this->tableName}");
            }
        }

        $this->primary_key = $primary_key;

        return $this;
    }

    /**
     * add primary key
     * @param string $column_name
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function addPrimaryKey($column_name)
    {
        if(is_string($column_name)) {
            $column_name = array($column_name);
        }

        foreach($column_name as $column) {
            if (! array_key_exists($column, $this->columns)) {
                throw new DdlGeneratorException("Column '{$column}' is not found in {$this->tableName}");
            }

            $this->primary_key[] = $column;
        }

        return $this;
    }

    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * add Column
     * @param Column $column
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function addColumn(Column $column)
    {
        $column_name = $column->getColumnName();
        if(array_key_exists($column_name, $this->columns)) {
            throw new DdlGeneratorException("Column '{$column_name}' is already exist in {$this->tableName}");
        }

        $this->columns[$column_name] = $column;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTableName();
    }
}
