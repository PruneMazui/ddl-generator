<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\AbstractDdlGenerator;

/**
 * Abstract Class DDL builder
 * @author ko_tanaka
 */
abstract class AbstractDdlBuilder extends AbstractDdlGenerator implements DdlBuilderInterface
{
    protected static $defaultConfig = [
        'end_of_line'       => "\n",
        'format'            => "UTF-8",
    ];

    protected $config = [];

    /**
     * Quote String
     * @param String $str
     */
    protected function quoteString($str)
    {
        return "'" . addcslashes($str, "\000\n\r\\'\"\032") . "'";
    }

    protected function encode($str)
    {
        $encode = $this->getConfig('format');

        if(! strlen($encode) || strtoupper($encode) == 'UTF-8') {
            return $str;
        }

        return mb_convert_encoding($str, $encode);
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildAll()
     */
    public function buildAll(Definition $definition, $add_drop_table = true)
    {
        // DROP TABLE
        $sql = '';
        if($add_drop_table) {
            $sql .= $this->buildAllDropTable($definition);
        }

        // CREATE TABLE
        $sql .= $this->buildAllCreateTable($definition);

        // INDEX
        $sql .= $this->buildAllCreateIndex($definition);

        // FOREIGN KEY
        $sql .= $this->buildAllCreateForeignKey($definition);

        return $sql;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildAllCreateTable()
     */
    public function buildAllCreateTable(Definition $definition)
    {
        if($definition->countSchemas() == 0) {
            return '';
        }

        $eol = $this->getConfig('end_of_line');

        $sql = '/** CREATE TABLE **/' . $eol;

        foreach($definition->getSchemas() as $schema) {
            foreach($schema->getTables() as $table) {
                $sql .= $this->buildCreateTable($schema, $table) . $eol . $eol;
            }
        }

        return $sql;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildAllCreateIndex()
     */
    public function buildAllCreateIndex(Definition $definition)
    {
        if($definition->countIndexes() == 0) {
            return '';
        }

        $eol = $this->getConfig('end_of_line');

        $sql = '/** CREATE INDEX **/' . $eol;
        foreach($definition->getIndexes() as $index) {
            $sql .= $this->buildCreateIndex($index) . $eol;
        }

        return $sql . $eol;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildAllCreateForeignKey()
     */
    public function buildAllCreateForeignKey(Definition $definition)
    {
        if($definition->countForeignKeys() == 0) {
            return '';
        }

        $eol = $this->getConfig('end_of_line');

        $sql = '/** CREATE FOREIGN KEY **/' . $eol;

        foreach($definition->getForeignKeys() as $foreign_key) {
            $sql .= $this->buildCreateForeignKey($foreign_key) . $eol;
        }

        return $sql . $eol;
    }
}
