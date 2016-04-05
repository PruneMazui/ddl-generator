<?php
namespace PruneMazui\DdlGenerator\DataSource;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\Definition\Rules\Table;

/**
 * Excel Table Definition
 *
 * @author ko_tanaka
 */
class ExcelDataSource extends AbstractDataSource
{
    protected static $defaultConfig = array(
        'filename'          => '',
        'sheets'            => '',
        'skip_first_line'   => true,
        'key_map'           => array(),
    );

    protected static $defaultKeyMap = array(
        self::TYPE_TABLE => array(
            Feild::SCHEMA_NAME            => "B",
            Feild::TABLE_NAME             => "C",
            Feild::TABLE_COMMENT          => "D",
            Feild::COLUMN_NAME            => "E",
            Feild::COLUMN_COMMENT         => "F",
            Feild::COLUMN_DATA_TYPE       => "G",
            Feild::COLUMN_LENGTH          => "H",
            Feild::COLUMN_REQUIRED        => "I",
            Feild::COLUMN_PRIMARY_KEY     => "J",
            Feild::COLUMN_AUTO_INCREMENT  => "K",
            Feild::COLUMN_DEFAULT         => "L",
        ),
        self::TYPE_INDEX => array(
            Feild::KEY_NAME         => "B",
            Feild::UNIQUE_INDEX       => "C",
            Feild::SCHEMA_NAME        => "D",
            Feild::TABLE_NAME         => "E",
            Feild::COLUMN_NAME        => "F",
        ),
        self::TYPE_FOREIGN_KEY => array(
            Feild::KEY_NAME           => "B",
            Feild::SCHEMA_NAME        => "C",
            Feild::TABLE_NAME         => "D",
            Feild::COLUMN_NAME        => "E",
            Feild::LOOKUP_SCHEMA_NAME => "F",
            Feild::LOOKUP_TABLE_NAME  => "G",
            Feild::LOOKUP_COLUMN_NAME => "H",
            Feild::ON_UPDATE          => "I",
            Feild::ON_DELETE          => "J",
        ),
    );

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DataSource\DataSourceInterface::read()
     */
    public function read()
    {
        $filename = $this->getConfig('filename');
        if(! strlen($filename)) {
            throw new DdlGeneratorException('filename is required for config');
        }

        $reader = \PHPExcel_IOFactory::createReaderForFile($filename);
        $excel = $reader->load($filename);

        $sheets = $this->getConfig('sheets');
        if(is_string($sheets)) {
            $sheets = array($sheets);
        }

        $ret = array();
        $key_map = $this->getKeyMap();

        foreach($sheets as $sheet_name) {
            if(! strlen($sheet_name)) {
                throw new DdlGeneratorException('Sheet Name is required for config');
            }

            $sheet = $excel->getSheetByName($sheet_name);
            $skip_line = $this->getConfig('skip_first_line');

            foreach ($sheet->getRowIterator() as $row_number => $row) {

                if($skip_line) {
                    $skip_line = false;
                    continue;
                }

                $row_data = array();
                foreach ($row->getCellIterator() as $col_number => $cell) {
                    $row_data[$col_number] = $cell->getValue();
                }

                $row_data = array_filter($row_data);

                if(count($row_data)) {
                    $ret[] = new RowData($row_data, $key_map);
                }
            }
        }

        return $ret;
    }
}
