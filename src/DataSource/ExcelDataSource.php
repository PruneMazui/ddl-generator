<?php
namespace PruneMazui\DdlGenerator\DataSource;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\TableDefinition\TableDefinition;
use PruneMazui\DdlGenerator\TableDefinition\Schema;
use PruneMazui\DdlGenerator\TableDefinition\Table;
use PruneMazui\DdlGenerator\TableDefinition\Column;

/**
 * Table Definition Creater From PHPExcel
 *
 * $config => array(
 *    'filename' => $filename, (required)
 *    'tableSheets' =>
 * );
 *
 *
 * @author ko_tanaka
 */
class ExcelDataSource extends AbstractDataSource
{
    const EXCEL_SCHEMA_NAME = 'schema_name';
    const EXCEL_TABLE_NAME = 'table_name';
    const EXCEL_TABLE_COMMENT = 'table_comment';
    const EXCEL_COLUMN_NAME = 'column_name';
    const EXCEL_COLUMN_COMMENT = 'column_comment';
    const EXCEL_COLUMN_DATA_TYPE = 'column_data_type';
    const EXCEL_COLUMN_LENGTH = 'column_length';
    const EXCEL_COLUMN_REQUIRED = 'column_required';
    const EXCEL_COLUMN_PRIMARY_KEY = 'column_primary_key';
    const EXCEL_COLUMN_AUTO_INCREMENT = 'column_auto_increament';
    const EXCEL_COLUMN_DEFAULT = 'column_default';

    private static $defaultConfig = array(
        'filename'          => '',
        'skip_first_line'   => true,
        'table_sheet'       => '',
        'table_cell_number' => array(
            ExcelDataSource::EXCEL_SCHEMA_NAME            => "B",
            ExcelDataSource::EXCEL_TABLE_NAME             => "C",
            ExcelDataSource::EXCEL_TABLE_COMMENT          => "D",
            ExcelDataSource::EXCEL_COLUMN_NAME            => "E",
            ExcelDataSource::EXCEL_COLUMN_COMMENT         => "F",
            ExcelDataSource::EXCEL_COLUMN_DATA_TYPE       => "G",
            ExcelDataSource::EXCEL_COLUMN_LENGTH          => "H",
            ExcelDataSource::EXCEL_COLUMN_REQUIRED        => "I",
            ExcelDataSource::EXCEL_COLUMN_PRIMARY_KEY     => "J",
            ExcelDataSource::EXCEL_COLUMN_AUTO_INCREMENT  => "K",
            ExcelDataSource::EXCEL_COLUMN_DEFAULT         => "L",
        ),
    );

    /**
     * @param string $filename
     * @return \PHPExcel
     * @throws DdlGeneratorException
     */
    public function readFile($filename)
    {
        if(! strlen($filename)) {
            throw new DdlGeneratorException('filename is required for config');
        }

        $reader = \PHPExcel_IOFactory::createReaderForFile($filename);
        return $reader->load($filename);
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DataSource\DataSourceInterface::load()
     */
    public function load()
    {
        $config = $this->config + self::$defaultConfig;

        $filename = $config['filename'];
        $excel = $this->readFile($filename);

        $sheet_name = $config['table_sheet'];
        if(is_string($sheet_name)) {
            $sheet_name = array($sheet_name);
        }

        return $this->loadTableSheets($excel, $sheet_name, $config);
    }

    public function loadTableSheets(\PHPExcel $excel, array $sheets, array $config)
    {
        $getCell = function ($row_data, $key) use ($config) {
            $cell_number = $config['table_cell_number'];

            if (isset($row_data[$cell_number[$key]])) {
                return $row_data[$cell_number[$key]];
            }
            return null;
        };

        $definition = new TableDefinition();
        $schema = null;
        $table = null;

        foreach($sheets as $sheet_name) {
            if(! strlen($sheet_name)) {
                throw new DdlGeneratorException('Sheet Name is required for config');
            }

            $sheet = $excel->getSheetByName($sheet_name);
            $skip_line = $config['skip_first_line'];
            $row_number = null;
            $col_number = null;

            try {
                foreach ($sheet->getRowIterator() as $row_number => $row) {

                    if($skip_line) {
                        $skip_line = false;
                        continue;
                    }

                    $row_data = array();
                    foreach ($row->getCellIterator() as $col_number => $cell) {
                        $row_data[$col_number] = $cell->getValue();
                    }

                    $schema_name = $getCell($row_data, self::EXCEL_SCHEMA_NAME);
                    if(is_null($schema) || $schema_name) {
                        $schema = new Schema($schema_name);
                        $definition->addSchema($schema);
                    }

                    $table_name = $getCell($row_data, self::EXCEL_TABLE_NAME);
                    if (strlen($table_name)) {
                        $table_comment = $getCell($row_data, self::EXCEL_TABLE_COMMENT);
                        $table = new Table($table_name, $table_comment);
                        $schema->addTable($table);
                    }

                    if (is_null($table)) {
                        continue;
                    }

                    $column_name = $getCell($row_data, self::EXCEL_COLUMN_NAME);
                    if(strlen($column_name)) {
                        $data_type = $getCell($row_data, self::EXCEL_COLUMN_DATA_TYPE);
                        $required = $getCell($row_data, self::EXCEL_COLUMN_REQUIRED);
                        $length = $getCell($row_data, self::EXCEL_COLUMN_LENGTH);
                        $default = $getCell($row_data, self::EXCEL_COLUMN_DEFAULT);
                        $comment = $getCell($row_data, self::EXCEL_COLUMN_COMMENT);
                        $is_auto_increment = $getCell($row_data, self::EXCEL_COLUMN_AUTO_INCREMENT);

                        $column = new Column($column_name, $data_type, $required, $length, $default, $comment, $is_auto_increment);
                        $table->addColumn($column);

                        if($getCell($row_data, self::EXCEL_COLUMN_PRIMARY_KEY)) {
                            $table->addPrimaryKey($column_name);
                        }
                    }
                }
            } catch (DdlGeneratorException $ex) {
                throw new DdlGeneratorException("[{$row_number}, {$col_number}] " . $ex->getMessage(), null, $ex);
            }

        }

        return $definition;
    }
}
