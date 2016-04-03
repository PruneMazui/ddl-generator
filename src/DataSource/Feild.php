<?php
namespace PruneMazui\DdlGenerator\DataSource;

/**
 * Constant class DataSource Feild
 * @author tanaka
 */
class Feild
{
    // @todo index comment supported by mysql >= 5.5
    const SCHEMA_NAME = 'schema_name';
    const TABLE_NAME = 'table_name';
    const TABLE_COMMENT = 'table_comment';
    const COLUMN_NAME = 'column_name';
    const COLUMN_COMMENT = 'column_comment';
    const COLUMN_DATA_TYPE = 'column_data_type';
    const COLUMN_LENGTH = 'column_length';
    const COLUMN_REQUIRED = 'column_required';
    const COLUMN_PRIMARY_KEY = 'column_primary_key';
    const COLUMN_AUTO_INCREMENT = 'column_auto_increament';
    const COLUMN_DEFAULT = 'column_default';
    const KEY_NAME = 'key_name';
    const LOCKUP_SCHEMA_NAME = 'lockup_schema_name';
    const LOCKUP_TABLE_NAME = 'lockup_table_name';
    const LOCKUP_COLUMN_NAME = 'lockup_column_name';
    const ON_UPDATE = 'on_update';
    const ON_DELETE = 'on_delete';
    const INDEX_NAME = 'index_name';
    const UNIQUE_INDEX = 'unique_index';

    private function __construct()
    {}
}
