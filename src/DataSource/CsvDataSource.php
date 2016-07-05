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
            Feild::SCHEMA_NAME           => 0,
            Feild::TABLE_NAME            => 1,
            Feild::TABLE_COMMENT         => 2,
            Feild::COLUMN_NAME           => 3,
            Feild::COLUMN_COMMENT        => 4,
            Feild::COLUMN_DATA_TYPE      => 5,
            Feild::COLUMN_LENGTH         => 6,
            Feild::COLUMN_REQUIRED       => 7,
            Feild::COLUMN_PRIMARY_KEY    => 8,
            Feild::COLUMN_AUTO_INCREMENT => 9,
            Feild::COLUMN_DEFAULT        => 10,
        ),
        self::TYPE_INDEX => array(
            Feild::KEY_NAME   => 0,
            Feild::UNIQUE_INDEX => 1,
            Feild::SCHEMA_NAME  => 2,
            Feild::TABLE_NAME   => 3,
            Feild::COLUMN_NAME  => 4,
        ),
        self::TYPE_FOREIGN_KEY => array(
            Feild::KEY_NAME           => 0,
            Feild::SCHEMA_NAME        => 1,
            Feild::TABLE_NAME         => 2,
            Feild::COLUMN_NAME        => 3,
            Feild::LOOKUP_SCHEMA_NAME => 4,
            Feild::LOOKUP_TABLE_NAME  => 5,
            Feild::LOOKUP_COLUMN_NAME => 6,
            Feild::ON_UPDATE          => 7,
            Feild::ON_DELETE          => 8,
        )
    );

    /**
     * @return resource
     */
    private function fopenEncoding($filename)
    {
        $format = $this->getConfig('format');

        if(!strlen($format) || $format == 'UTF-8') {
            return @fopen($filename, 'r');
        }

        $content = file_get_contents($filename);
        $content = mb_convert_encoding($content, 'UTF-8', $format);

        $fp = tmpfile();
        fwrite($fp, $content);
        fseek($fp, 0);

        return $fp;
    }

    /**
     * {@inheritDoc}
     * @see \PruneMazui\DdlGenerator\DataSource\DataSourceInterface::read()
     */
    public function read()
    {
        $filename = $this->getConfig('filename');
        if (! strlen($filename)) {
            throw new DdlGeneratorException('filename is required for config');
        }

        if (! file_exists($filename) || ! is_file($filename) || ! is_readable($filename)) {
            throw new DdlGeneratorException("'{$filename}' is not readable");
        }

        $fp = $this->fopenEncoding($filename);
        if($fp === false) {
            throw new DdlGeneratorException("faild to file open.");
        }

        $skip_line = $this->getConfig('skip_first_line');

        $ret = array();
        $line_count = 0;
        $key_map = $this->getKeyMap();

        while (! feof($fp)) {
            $line = fgetcsv($fp);

            if ($skip_line) {
                $skip_line = false;
                $line_count++;
                continue;
            }

            if(is_array($line)) {
                $ret[$line_count] = new RowData($line, $key_map);
            }
            $line_count++;
        }

        fclose($fp);

        return $ret;
    }
}
