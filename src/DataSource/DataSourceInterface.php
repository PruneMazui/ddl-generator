<?php
namespace PruneMazui\DdlGenerator\DataSource;

use PruneMazui\DdlGenerator\TableDefinition\TableDefinition;

/**
 * 読み込みデータソース
 *
 * @author ko_tanaka
 */
interface DataSourceInterface
{
    /**
     * @return \PruneMazui\DdlGenerator\TableDefinition\TableDefinition
     */
    public function load();
}
