<?php
namespace PruneMazui\DdlGeneratorTest;

require_once __DIR__ . '/../vendor/autoload.php';

$reflection = new ReflectionClass('PHPUnit_Framework_Assert');
require_once dirname($reflection->getFileName()) . '/Assert/Functions.php';

if(file_exists(__DIR__ . '/config/config.php')) {
    $config = include __DIR__ . '/config/config.php';

    if(isset($config['mysql']) && ! empty($config['mysql'])) {
        $cfg = $config['mysql'];
        $pdo = new \PDO($cfg['dsn'], $cfg['username'], $cfg['password'], $cfg['options']);
        AbstractTestCase::setMySql($pdo);
    }
}
