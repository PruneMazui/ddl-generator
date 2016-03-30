<?php
namespace PruneMazui\DdlGenerator\DataSource;

/**
 *
 * @author ko_tanaka
 */
abstract class AbstractDataSource implements DataSourceInterface
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
     * @return \PruneMazui\DdlGenerator\DataSource\AbstractDataSource
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }
}
