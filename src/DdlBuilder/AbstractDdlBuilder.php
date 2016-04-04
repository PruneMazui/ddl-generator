<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\Definition\Definition;

/**
 * Abstract Class DDL builder
 * @author ko_tanaka
 */
abstract class AbstractDdlBuilder implements DdlBuilderInterface
{
    protected static $defaultConfig = array(
        'end_of_line'       => "\n",
    );

    protected $config = array();

    /**
     * @param array optional $config
     */
    public function __construct(array $config = null)
    {
        if(! is_null($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * Set Config
     * @param array $config
     * @return \PruneMazui\DdlGenerator\DdlBuilder\AbstractDdlBuilder
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get Config
     * @param config $key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        $config = $this->config + static::$defaultConfig;

        if(is_null($key)) {
            return $config;
        }

        if(isset($config[$key])) {
            return $config[$key];
        }

        return null;
    }

    /**
     * Quote String
     * @param String $str
     */
    protected function quoteString($str)
    {
        return "'" . addcslashes($str, "\000\n\r\\'\"\032") . "'";
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
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildAllDropTable()
     */
    public function buildAllDropTable(Definition $definition)
    {
        if($definition->countSchemas() == 0) {
            return '';
        }

        $eol = $this->getConfig('end_of_line');

        $sql = '/** DROP TABLE **/' . $eol;

        foreach($definition->getSchemas() as $schema) {
            foreach($schema->getTables() as $table) {
                $sql .= $this->buildDropTable($schema, $table) . $eol;
            }
        }

        return $sql . $eol;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildAllCreateIndex()
     */
    public function buildAllCreateIndex(Definition $definition)
    {
        if($definition->countAllIndexes() == 0) {
            return '';
        }

        $eol = $this->getConfig('end_of_line');

        $sql = '/** CREATE INDEX **/' . $eol;
        foreach($definition->getSchemas() as $schema) {
            foreach($schema->getTables() as $table) {
                foreach($table->getIndexes() as $index){
                    $sql .= $this->buildCreateIndex($index) . $eol;
                }
            }
        }

        return $sql . $eol;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DdlBuilder\DdlBuilderInterface::buildAllCreateForeignKey()
     */
    public function buildAllCreateForeignKey(Definition $definition)
    {
        if($definition->countAllForeignKeys() == 0) {
            return '';
        }

        $eol = $this->getConfig('end_of_line');

        $sql = '/** CREATE FOREIGN KEY **/' . $eol;

        foreach($definition->getSchemas() as $schema) {
            foreach($schema->getTables() as $table) {
                foreach($table->getForeignKeys() as $foreign_key){
                    $sql .= $this->buildCreateForeignKey($foreign_key) . $eol;
                }
            }
        }

        return $sql . $eol;
    }
}
