<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

use PruneMazui\DdlGenerator\DdlGeneratorException;

/**
 * Column
 *
 * @author ko_tanaka
 */
class Column extends AbstractRules
{
    private $columnName;

    private $dataType;

    private $required = false;

    private $length = null;

    private $default = null;

    private $comment = '';

    private $isAutoIncrement = false;

    /**
     * @param string $column_name
     * @param string $dataType
     * @param bool $required
     * @param string $length
     * @param string $default
     * @param string $comment
     * @param bool $is_auto_increment
     */
    public function __construct($column_name, $data_type, $required = null, $length = null, $default = null, $comment = null, $is_auto_increment = null)
    {
        if(! strlen($column_name)) {
            throw new DdlGeneratorException('Column Name is not allow empty.');
        }

        if(! strlen($data_type)) {
            throw new DdlGeneratorException('Data type is not allow empty.');
        }

        $this->columnName = $column_name;
        $this->dataType = $data_type;

        if (!is_null($required)) {
            $this->required = !! $required;
        }

        if (!is_null($length)) {
            $this->length = $length;
        }

        if (!is_null($default)) {
            $this->default = $default;
        }

        if (!is_null($comment)) {
            $this->comment = $comment;
        }

        if (!is_null($is_auto_increment)) {
            $this->isAutoIncrement = !! $is_auto_increment;
        }
    }

    public function getColumnName()
    {
        return $this->columnName;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function isAutoIncrement()
    {
        return $this->isAutoIncrement;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getColumnName();
    }
}
