<?php
namespace PruneMazui\DdlGeneratorTest;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DbWrapper
     */
    private static $mysql = null;

    /**
     * @param \PDO $pdo
     */
    public static function setMySql(DbWrapper $db)
    {
        self::$mysql = $db;
    }

    /**
     * @return bool
     */
    protected function hasMySql()
    {
        return ! is_null(self::$mysql);
    }

    /**
     * @return DbWrapper
     */
    protected function getMySql()
    {
        return self::$mysql;
    }
}
