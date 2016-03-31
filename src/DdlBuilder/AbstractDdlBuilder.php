<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\TableDefinition\TableDefinition;

/**
 * Abstract Class DDL builder
 * @author ko_tanaka
 */
abstract class AbstractDdlBuilder implements DdlBuilderInterface
{
    protected static $defaultConfig = array();

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
     */
    protected function getConfig($key)
    {
        $config = $this->config + static::$defaultConfig;

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
    public function buildAll(TableDefinition $definition, $add_drop_table = true)
    {
        $config = $this->config + self::$defaultConfig;

        // DROP TABLE 構文
        $sql = '';
        if($add_drop_table) {
            $sql .= $this->buildAllDropTable($definition);
        }

        // CREATE TABLE 構文
        $sql .= $this->buildAllCreateTable($definition);

        return $sql;
    }
}
