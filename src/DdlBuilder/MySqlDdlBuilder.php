<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;
use PruneMazui\DdlGenerator\Definition\Rules\Index;

/**
 * DDL for MySQL
 *
 * @author ko_tanaka
 */
class MySqlDdlBuilder extends AbstractDdlBuilder
{
    protected static $defaultConfig = [
        'add_empty_string'  => true, // if colmn's data type is (var)char and required, empty string is added to the default value
        'end_of_line'       => "\n",
        'indent'            => "    ",
        'format'            => "UTF-8",
    ];

    protected static $numericTypeMap = [
        'INT', // INTEGER INT SMALLINT TINYINT MEDIUMINT BIGINT
        'DEC', 'FIXED', 'NUMERIC', 'FIXED', // DECIMAL alias
        'BIT', 'BOOL', // TINYINT(1)
        'FLOAT', 'DOUBLE', 'REAL',
    ];

    /**
     *
     * @param string $data_type
     * @return bool
     */
    public function isNumericType($data_type)
    {
        $data_type = strtoupper($data_type);

        foreach(self::$numericTypeMap as $word) {
            if(strpos($data_type, $word) !== false) {
                return true;
            }
        }

        return false;
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
     * @see \PruneMazui\DdlGenerator\DdlBuilder\AbstractDdlBuilder::buildAll()
     */
    public function buildAll(Definition $definition, $add_drop_table = true)
    {
        if($definition->countSchemas() > 1) {
            throw new DdlGeneratorException('There are no schema in MySQL. Schema count is grater than 1.');
        }

        return parent::buildAll($definition, $add_drop_table);
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\AbstractDdlBuilder::buildAllCreateTable()
     */
    public function buildAllCreateTable(Definition $definition)
    {
        if($definition->countSchemas() > 1) {
            throw new DdlGeneratorException('There are no schema in MySQL. Schema count is grater than 1.');
        }

        return parent::buildAllCreateTable($definition);
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildCreateTable()
     */
    public function buildCreateTable(Schema $schema, Table $table)
    {
        $eol = $this->getConfig('end_of_line');
        $indent = $this->getConfig('indent');

        $sql  = 'CREATE TABLE ' . $this->quoteIdentifier($table->getTableName()) . ' (' . $eol;
        $sql .= $this->buildTableColumns($table);

        $primary_key = $table->getPrimaryKey();
        if(count($primary_key)) {

            foreach($primary_key as $key => $value) {
                $primary_key[$key] = $this->quoteIdentifier($value);
            }

            $sql .= ',' . $eol . $eol . $indent . 'PRIMARY KEY (' . implode(', ', $primary_key) . ')' . $eol;
        }

        $sql .= ')';
        $comment = $table->getComment();
        if(strlen($comment)) {
            $sql .= ' COMMENT=' . $this->quoteString($comment);
        }
        $sql .= ';';

        return $this->encode($sql);
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildAllDropTable()
     */
    public function buildAllDropTable(Definition $definition)
    {
        if($definition->countSchemas() == 0) {
            return '';
        }

        if($definition->countSchemas() > 1) {
            throw new DdlGeneratorException('There are no schema in MySQL. Schema count is grater than 1.');
        }

        $eol = $this->getConfig('end_of_line');

        $sql =
        '/** DROP TABLE **/' . $eol .
        'SET @@session.SQL_NOTES = 0;' . $eol .
        'SET @@session.FOREIGN_KEY_CHECKS = 0;' . $eol
        ;

        foreach($definition->getSchemas() as $schema) {
            foreach($schema->getTables() as $table) {
                $sql .= $this->buildDropTable($schema, $table) . $eol;
            }
        }

        $sql .=
        'SET @@session.SQL_NOTES = DEFAULT;' . $eol .
        'SET @@session.FOREIGN_KEY_CHECKS = DEFAULT;' . $eol
        ;

        return $sql . $eol;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildDropTable()
     */
    public function buildDropTable(Schema $schema, Table $table)
    {
        return $this->encode('DROP TABLE IF EXISTS ' . $this->quoteIdentifier($table->getTableName()) . ';');
    }

    private function buildTableColumns(Table $table)
    {
        $addEmptyString = $this->getConfig('add_empty_string');
        $eol = $this->getConfig('end_of_line');
        $indent = $this->getConfig('indent');

        $lines = [];
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

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\AbstractDdlBuilder::buildAllCreateIndex()
     */
    public function buildAllCreateIndex(Definition $definition)
    {
        if($definition->countSchemas() > 1) {
            throw new DdlGeneratorException('There are no schema in MySQL. Schema count is grater than 1.');
        }

        return parent::buildAllCreateIndex($definition);
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildCreateIndex()
     */
    public function buildCreateIndex(Index $index)
    {
        $sql = "CREATE ";
        if($index->isUniqueIndex()) {
            $sql .= "UNIQUE ";
        }

        // ignore schema
        $sql .= "INDEX " . $this->quoteIdentifier($index->getKeyName())
            . " ON " . $this->quoteIdentifier($index->getTableName());

        $columns = array_map([$this, 'quoteIdentifier'], $index->getColumnNameList());

        $sql .= " (" . implode(", ", $columns) . ");";

        return $this->encode($sql);
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\AbstractDdlBuilder::buildAllCreateForeignKey()
     */
    public function buildAllCreateForeignKey(Definition $definition)
    {
        if($definition->countSchemas() > 1) {
            throw new DdlGeneratorException('There are no schema in MySQL. Schema count is grater than 1.');
        }

        return parent::buildAllCreateForeignKey($definition);
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildCreateForeignKey()
     */
    public function buildCreateForeignKey(ForeignKey $foreign_key)
    {
        $columns = array_map([$this, 'quoteIdentifier'], $foreign_key->getColumnNameList());
        $lookup_columns = array_map([$this, 'quoteIdentifier'], $foreign_key->getLookupColumnNameList());

        // ignore schema
        $sql = "ALTER TABLE " . $this->quoteIdentifier($foreign_key->getTableName())
            . " ADD CONSTRAINT " . $this->quoteIdentifier($foreign_key->getKeyName())
            . " FOREIGN KEY (" . implode(", ", $columns) . ")"
            . " REFERENCES " . $this->quoteIdentifier($foreign_key->getLookupTableName())
            . " (" . implode(", ", $lookup_columns) . ")"
            . " ON UPDATE " . $foreign_key->getOnUpdate()
            . " ON DELETE " . $foreign_key->getOnDelete() . ";";

        return $this->encode($sql);
    }
}
