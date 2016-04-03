<?php
namespace PruneMazui\DdlGenerator\DataSource;

use PruneMazui\DdlGenerator\AbstractDdlGenerator;
use PruneMazui\DdlGenerator\DdlGeneratorException;

/**
 *
 * @author ko_tanaka
 */
abstract class AbstractDataSource extends AbstractDdlGenerator implements DataSourceInterface
{
    const TYPE_TABLE = 'table';
    const TYPE_FOREIGN_KEY = 'foreign_key';
    const TYPE_INDEX = 'index';

    protected $datasource_type = self::TYPE_TABLE; // default

    protected static $defaultKeyMap = array(
        self::TYPE_TABLE       => array(),
        self::TYPE_INDEX       => array(),
        self::TYPE_FOREIGN_KEY => array(),
    );

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DataSource\DataSourceInterface::setDataSourceType()
     */
    public function setDataSourceType($type)
    {
        if(! in_array($type, array(
            self::TYPE_TABLE,
            self::TYPE_INDEX,
            self::TYPE_FOREIGN_KEY,
        ))) {
            throw new DdlGeneratorException('Unkown DataSource Type : ' . $type);
        }

        $this->datasource_type = $type;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DataSource\DataSourceInterface::getKeyMap()
     */
    public function getKeyMap()
    {
        $key_map = $this->getConfig('key_map');
        if(empty($key_map)) {
            $key_map = static::$defaultKeyMap[$this->datasource_type];
        }

        if(empty($key_map)) {
            throw new DdlGeneratorException('key mapping is not defined.');
        }

        return $key_map;
    }
}
