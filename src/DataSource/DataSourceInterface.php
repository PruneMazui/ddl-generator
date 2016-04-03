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
     * @return \PruneMazui\DdlGenerator\DataSource\RowData[]
     */
    public function read();

    /**
     * Set DataSource Type
     * @param string $type
     * @return self
     */
    public function setDataSourceType($type);

    /**
     * get releation from feild name to array offset
     *
     * @return array
     */
    public function getKeyMap();
}
