<?php
namespace PruneMazui\DdlGenerator;

class DdlGenerator
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    
}
