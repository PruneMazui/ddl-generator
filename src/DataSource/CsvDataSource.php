<?php
namespace PruneMazui\DdlGenerator\DataSource;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\Definition\Rules\Table;

/**
 * Csv Table Definition
 *
 * @author ko_tanaka
 */
class CsvDataSource extends AbstractDataSource
{

    protected static $defaultConfig = array(
        'filename' => '',
        'format' => 'UTF-8',
        'skip_first_line' => true,
        'key_map' => array()
    );

    protected static $defaultKeyMap = array(
        self::TYPE_TABLE => array(
            self::FEILD_SCHEMA_NAME           => 0,
            self::FEILD_TABLE_NAME            => 1,
            self::FEILD_TABLE_COMMENT         => 2,
            self::FEILD_COLUMN_NAME           => 3,
            self::FEILD_COLUMN_COMMENT        => 4,
            self::FEILD_COLUMN_DATA_TYPE      => 5,
            self::FEILD_COLUMN_LENGTH         => 6,
            self::FEILD_COLUMN_REQUIRED       => 7,
            self::FEILD_COLUMN_PRIMARY_KEY    => 8,
            self::FEILD_COLUMN_AUTO_INCREMENT => 9,
            self::FEILD_COLUMN_DEFAULT        => 10,
        ),
        self::TYPE_INDEX => array(
            self::FEILD_INDEX_NAME => "B",
            self::FEILD_UNIQUE_INDEX => "C",
            self::FEILD_SCHEMA_NAME => "D",
            self::FEILD_TABLE_NAME => "E",
            self::FEILD_COLUMN_NAME => "F"
        ),
        self::TYPE_FOREIGN_KEY => array(
            self::FEILD_KEY_NAME           => 0,
            self::FEILD_SCHEMA_NAME        => 1,
            self::FEILD_TABLE_NAME         => 2,
            self::FEILD_COLUMN_NAME        => 3,
            self::FEILD_LOCKUP_SCHEMA_NAME => 4,
            self::FEILD_LOCKUP_TABLE_NAME  => 5,
            self::FEILD_LOCKUP_COLUMN_NAME => 6,
            self::FEILD_ON_UPDATE          => 7,
            self::FEILD_ON_DELETE          => 8,
        )
    );

    /**
     * @return resource
     */
    private function fopenEncoding($filename)
    {
        $format = $this->getConfig('format');

        if(!strlen($format) || $format == 'UTF-8') {
            return fopen($filename, 'r');
        }

        $content = file_get_contents($filename);
        $content = mb_convert_encoding($content, 'UTF-8', $format);

        $fp = tmpfile();
        fwrite($fp, $content);
        fseek($fp, 0);

        return $fp;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \PruneMazui\DdlGenerator\DataSource\DataSourceInterface::read()
     */
    public function read()
    {
        $filename = $this->getConfig('filename');
        if (! strlen($filename)) {
            throw new DdlGeneratorException('filename is required for config');
        }

        if (!file_exists($filename) || !is_readable($filename)) {
            throw new DdlGeneratorException("'{$filename}' is not readable");
        }

        $fp = $this->fopenEncoding($filename);
        $skip_line = $this->getConfig('skip_first_line');

        $ret = array();
        $line_count = 0;

        while (! feof($fp)) {
            $line = fgetcsv($fp);

            if ($skip_line) {
                $skip_line = false;
                $line_count++;
                continue;
            }

            $ret[$line_count] = $line;
            $line_count++;
        }

        return $ret;
    }
}
