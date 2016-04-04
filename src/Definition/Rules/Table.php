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
     * @var \PruneMazui\DdlGenerator\Definition\Rules\ForeignKey[]
     */
    private $foreignKeys = array();

    /**
     * @var \PruneMazui\DdlGenerator\Definition\Rules\Index[]
     */
    private $indexes = array();

    /**
     * @return number
     */
    public function countColumns()
    {
        return count($this->columns);
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
     * add index
     * @param Index $index
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Definition
     */
    public function addIndex(Index $index)
    {
        $key_name = $index->getKeyName();
        if(array_key_exists($key_name, $this->indexes)) {
            throw new DdlGeneratorException("Key '{$key_name}' is already exist in `{$this->getTableName()}`");
        }

        if(array_key_exists($key_name, $this->foreignKeys)) {
            throw new DdlGeneratorException("Key '{$key_name}' is already exist in `{$this->getTableName()}`");
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
        $key_name = $foreign_key->getKeyName();
        if(array_key_exists($key_name, $this->indexes)) {
            throw new DdlGeneratorException("Key '{$key_name}' is already exist in `{$this->getTableName()}`");
        }

        if(array_key_exists($key_name, $this->foreignKeys)) {
            throw new DdlGeneratorException("Key '{$key_name}' is already exist in `{$this->getTableName()}`");
        }

        $this->foreignKeys[$key_name] = $foreign_key;
        return $this;
    }

    /**
     * unset non column index and key
     * @throws DdlGeneratorException
     * @return \PruneMazui\DdlGenerator\Definition\Rules\Table
     */
    public function filter()
    {
        foreach($this->indexes as $key => $index) {
            foreach ($index->getColumnNameList() as $column_name) {
                if(! $this->hasColumn($column_name)) {
                    throw new DdlGeneratorException("Column `{$column_name}` is not found for index.");
                }
            }

            if($index->countColumns() == 0) {
                // @todo logging
                unset($this->indexes[$key]);
            }
        }

        foreach($this->foreignKeys as $key => $foreign_key) {
            $column_list = $foreign_key->getColumnNameList();
            $lookup_column_list = $foreign_key->getLookupColumnNameList();

            if(count($column_list) !== count($lookup_column_list)) {
                throw new DdlGeneratorException("Column count and lookup column count is not match.");
            }

            if($foreign_key->countColumns() == 0) {
                // @todo logging
                unset($this->foreignKeys[$key]);
                continue;
            }

            foreach($column_list as $idx => $column_name) {
                $lookup_column_name = $lookup_column_list[$idx];

                if(! $this->hasColumn($column_name)) {
                    throw new DdlGeneratorException("Column `{$column_name}` is not found for foreign key.");
                }

                if(! $foreign_key->getLookupTable()->hasColumn($lookup_column_name)) {
                    throw new DdlGeneratorException("Lookup Column `{$lookup_column_name}` is not found for foreign key.");
                }

                $column = $this->getColumn($column_name);
                $lookup_column = $foreign_key->getLookupTable()->getColumn($lookup_column_name);

                if($column->getDataType() != $lookup_column->getDataType()) {
                    // @todo notice foreign key column's data type is not equals.
                }
            }
        }

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
