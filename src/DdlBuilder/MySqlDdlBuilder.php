<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\TableDefinition\TableDefinition;
use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\TableDefinition\Table;

class MySqlDdlBuilder extends AbstractDdlBuilder
{
    private static $defaultConfig = array(
        'add_drop_table'    => true,
        'add_empty_string'  => true, // if colmn's data type is (var)char and required, empty string is added to the default value
        'end_of_line'       => "\n",
        'format'            => "UTF-8",
    );

    public function isNumericType($data_type)
    {
        static $map = array(
            'INT', // INTEGER INT SMALLINT TINYINT MEDIUMINT BIGINT
            'DEC', 'FIXED', 'NUMERIC', 'FIXED', // DECIMAL alias
            'BIT', 'BOOL', // TINYINT(1)
            'FLOAT', 'DOUBLE', 'REAL',
        );

        $data_type = strtoupper($data_type);

        foreach($map as $word) {
            if(strpos($data_type, $word) !== false) {
                return true;
            }
        }

        false;
    }

    /**
     * Quote identifier for MySQL
     * @param string $field_name
     * @return string
     */
    private function quoteIdentifier($field_name)
    {
        $field_name = str_replace('`', '``', $field_name);

        return '`' . $field_name . '`';
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::build()
     */
    public function build(TableDefinition $definition)
    {
        $config = $this->config + self::$defaultConfig;

        $schemas = $definition->getSchemas();

        if(count($schemas) > 1) {
            throw new DdlGeneratorException('There are no schemata in MySQL. Schema count is grater than 1.');
        }

        $eol = $config['end_of_line'];

        // DROP 構文
        $sql = '';
        if($config['add_drop_table']) {
            $sql .= $this->buildDropTable($definition, $eol);
        }

        $sql .= $this->buildCreateTable($definition, $config['add_empty_string'] ,$eol);

        return $sql;
    }

    public function buildDropTable(TableDefinition $definition, $eol)
    {
        $sql =
            '/** DROP TABLE **/' . $eol .
            'SET @@session.SQL_NOTES = 0;' . $eol .
            'SET @@session.FOREIGN_KEY_CHECKS = 0;' . $eol
        ;

        foreach($definition->getSchemas() as $schema) {
            foreach($schema->getTables() as $table) {
                $sql .= 'DROP TABLE IF EXISTS ' . $this->quoteIdentifier($table->getTableName()) . ';' . $eol;
            }
        }

        $sql .=
            'SET @@session.SQL_NOTES = DEFAULT;' . $eol .
            'SET @@session.FOREIGN_KEY_CHECKS = DEFAULT;' . $eol
        ;

        return $sql . $eol;
    }

    public function buildCreateTable(TableDefinition $definition, $addEmptyString ,$eol)
    {
        $indent = '    ';
        $sql = '/** CREATE TABLE **/';

        foreach($definition->getSchemas() as $schema) {

            foreach($schema->getTables() as $table) {
                $sql .= $eol. 'CREATE TABLE ' . $this->quoteIdentifier($table->getTableName()) . ' (' . $eol;
                $sql .= $this->buildTableColumns($table, $indent, $addEmptyString, $eol);

                $primary_key = $table->getPrimaryKey();
                if(count($primary_key)) {

                    foreach($primary_key as $key => $value) {
                        $primary_key[$key] = $this->quoteIdentifier($value);
                    }

                    $sql .= ',' . $eol . $eol . $indent . 'PRIMARY KEY ( ' . implode(', ', $primary_key) . ' )' . $eol;
                }

                $sql .= ')';
                $comment = $table->getComment();
                if(strlen($comment)) {
                    $sql .= ' COMMENT=' . $this->quoteString($comment);
                }
                $sql .= ';' . $eol;
            }
        }

        return $sql;
    }

    public function buildTableColumns(Table $table, $indent, $addEmptyString, $eol)
    {
        $lines = array();
        foreach($table->getColumns() as $column) {
            $sql = '';
            $sql .= $indent . $this->quoteIdentifier($column->getColumnName()) . ' ';

            $data_type = $column->getDataType();
            $sql .= $data_type;

            $length = $column->getLength();
            if(strlen($length)) {
                $sql .= '(' . $length . ')';
            }
            $sql .= ' ';

            if($column->isRequired()) {
                $sql .= 'NOT NULL ';
            }

            $default = $column->getDefault();
            if(strlen($default)) {
                if($this->isNumericType($data_type)) {
                    $sql .= 'DEFAULT ' . $default . " ";
                } else {
                    $sql .= 'DEFAULT ' . $this->quoteString($default) . " ";
                }
            } else if($addEmptyString && strpos(strtoupper($data_type), 'CHAR') !== false) {
                $sql .= "DEFAULT '' ";
            }

            if($column->isAutoIncrement()) {
                $sql .= "AUTO_INCREMENT ";
            }

            $comment = $column->getComment();
            if(strlen($comment)) {
                $sql .= 'COMMENT ' . $this->quoteString($comment);
            }

            $lines[] = $sql;
        }

        return implode(',' . $eol, $lines);
    }


}
