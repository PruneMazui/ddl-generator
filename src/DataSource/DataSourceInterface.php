<?php
namespace PruneMazui\DdlGenerator\DataSource;

/**
 * データソース
 *
 * @author ko_tanaka
 */
interface DataSourceInterface
{
    /**
     * @return string[][]
     */
    public function read();

    /**
     * Set DataSource Type
     * @param string $type
     * @return self
     */
    public function setDataSourceType($type);

    /**
     * Get key mapping
     * @return array
     */
    public function getKeyMap($feild = null);

}
