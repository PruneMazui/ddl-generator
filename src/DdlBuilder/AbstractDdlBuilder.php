<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

abstract class AbstractDdlBuilder implements DdlBuilderInterface
{
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
     * Quote String
     * @param String $str
     */
    protected function quoteString($str)
    {
        return "'" . addcslashes($str, "\000\n\r\\'\"\032") . "'";
    }
}
