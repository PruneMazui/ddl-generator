<?php
namespace PruneMazui\DdlGeneratorTest;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PDO
     */
    private static $mysql = null;

    /**
     * @param \PDO $pdo
     */
    public static function setMySql(\PDO $pdo)
    {
        self::$mysql = $pdo;
    }

    /**
     * @return bool
     */
    protected function hasMySql()
    {
        return ! is_null(self::$mysql);
    }

    /**
     * @return \PDO
     */
    protected function getMySql()
    {
        return self::$mysql;
    }
}
