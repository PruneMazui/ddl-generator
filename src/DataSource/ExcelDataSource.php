<?php
namespace PruneMazui\DdlGenerator\DataSource;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\Definition\Rules\Table;

/**
 * Table Definition Creater From PHPExcel
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
            self::FEILD_SCHEMA_NAME            => "B",
            self::FEILD_TABLE_NAME             => "C",
            self::FEILD_TABLE_COMMENT          => "D",
            self::FEILD_COLUMN_NAME            => "E",
            self::FEILD_COLUMN_COMMENT         => "F",
            self::FEILD_COLUMN_DATA_TYPE       => "G",
            self::FEILD_COLUMN_LENGTH          => "H",
            self::FEILD_COLUMN_REQUIRED        => "I",
            self::FEILD_COLUMN_PRIMARY_KEY     => "J",
            self::FEILD_COLUMN_AUTO_INCREMENT  => "K",
            self::FEILD_COLUMN_DEFAULT         => "L",
        ),
        self::TYPE_INDEX => array(
            // @todo index comment supported by mysql >= 5.5
            self::FEILD_INDEX_NAME         => "B",
            self::FEILD_UNIQUE_INDEX       => "C",
            self::FEILD_SCHEMA_NAME        => "D",
            self::FEILD_TABLE_NAME         => "E",
            self::FEILD_COLUMN_NAME        => "F",
        ),
        self::TYPE_FOREIGN_KEY => array(
            self::FEILD_KEY_NAME           => "B",
            self::FEILD_SCHEMA_NAME        => "C",
            self::FEILD_TABLE_NAME         => "D",
            self::FEILD_COLUMN_NAME        => "E",
            self::FEILD_LOCKUP_SCHEMA_NAME => "F",
            self::FEILD_LOCKUP_TABLE_NAME  => "G",
            self::FEILD_LOCKUP_COLUMN_NAME => "H",
            self::FEILD_ON_UPDATE          => "I",
            self::FEILD_ON_DELETE          => "J",
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
                $ret[] = $row_data;
            }
        }

        return $ret;
    }
}
