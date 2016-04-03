<?php
namespace PruneMazui\DdlGenerator\DataSource;

use PruneMazui\DdlGenerator\AbstractDdlGenerator;

/**
 *
 * @author ko_tanaka
 */
abstract class AbstractDataSource extends AbstractDdlGenerator implements DataSourceInterface
{
    const TYPE_TABLE = 'table';
    const TYPE_FOREIGN_KEY = 'foreign_key';
    const TYPE_INDEX = 'index';

    // @todo index comment supported by mysql >= 5.5
    const FEILD_SCHEMA_NAME = 'schema_name';
    const FEILD_TABLE_NAME = 'table_name';
    const FEILD_TABLE_COMMENT = 'table_comment';
    const FEILD_COLUMN_NAME = 'column_name';
    const FEILD_COLUMN_COMMENT = 'column_comment';
    const FEILD_COLUMN_DATA_TYPE = 'column_data_type';
    const FEILD_COLUMN_LENGTH = 'column_length';
    const FEILD_COLUMN_REQUIRED = 'column_required';
    const FEILD_COLUMN_PRIMARY_KEY = 'column_primary_key';
    const FEILD_COLUMN_AUTO_INCREMENT = 'column_auto_increament';
    const FEILD_COLUMN_DEFAULT = 'column_default';
    const FEILD_KEY_NAME = 'key_name';
    const FEILD_LOCKUP_SCHEMA_NAME = 'lockup_schema_name';
    const FEILD_LOCKUP_TABLE_NAME = 'lockup_table_name';
    const FEILD_LOCKUP_COLUMN_NAME = 'lockup_column_name';
    const FEILD_ON_UPDATE = 'on_update';
    const FEILD_ON_DELETE = 'on_delete';
    const FEILD_INDEX_NAME = 'index_name';
    const FEILD_UNIQUE_INDEX = 'unique_index';

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
        $this->datasource_type = $type;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DataSource\DataSourceInterface::getKeyMap()
     */
    public function getKeyMap($feild = null)
    {
        $key_map = $this->getConfig('key_map');
        if(empty($key_map)) {
            $key_map = static::$defaultKeyMap[$this->datasource_type];
        }

        if(is_null($feild)) {
            return $key_map;
        }

        return isset($key_map[$feild]) ? $key_map[$feild] : null;
    }
}
