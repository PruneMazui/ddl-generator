<?php
namespace PruneMazui\DdlGeneratorTest;

use Psr\Log\NullLogger;

require_once __DIR__ . '/../vendor/autoload.php';

$reflection = new \ReflectionClass('PHPUnit_Framework_Assert');
require_once dirname($reflection->getFileName()) . '/Assert/Functions.php';

if(file_exists(__DIR__ . '/config/config.php')) {
    $config = include __DIR__ . '/config/config.php';

    if(isset($config['mysql']) && ! empty($config['mysql'])) {
        $cfg = $config['mysql'];

        AbstractTestCase::setMySql(new DbWrapper($cfg));
    }
}

class DbWrapper
{
    private $pdo;

    public function __construct(array $cfg)
    {
        $this->pdo = new \PDO($cfg['dsn'], $cfg['username'], $cfg['password'], $cfg['options']);
    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * @param string $sql
     * @param array $bind
     * @return \PDOStatement
     */
    private function stmt($sql, array $bind=array())
    {
        $stmt = $this->pdo->prepare((string) $sql);
        $stmt->execute($bind);
        return $stmt;
    }

    /**
     * @param string $sql
     */
    public function fetchAll($sql, array $bind = array())
    {
        return $this->stmt($sql, $bind)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchColmun($sql, array $bind=array())
    {
        return $this->stmt($sql, $bind)->fetchAll(\PDO::FETCH_COLUMN);
    }
}

class TestLogger extends NullLogger
{
    private $messages = array();

    public function countMessages()
    {
        return count($this->messages);
    }

    public function hasMessages()
    {
        return !! count($this->messages);
    }

    public function resetMessages()
    {
        $this->messages = array();
        return $this;
    }

    public function log($level, $message, array $context = array())
    {
        $this->messages[] = $message;
    }
}
