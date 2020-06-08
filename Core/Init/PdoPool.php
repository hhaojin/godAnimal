<?php

namespace Core\Init;

use Core\Annotations\Bean;
use Core\Lib\DbPool;
use Swoole\Runtime;

/**
 * Class PdoPool
 * @Bean()
 */
class PdoPool extends DbPool
{

    public function __construct()
    {
        global $GLOBAL_CONFIG;
        $poolConfig = $GLOBAL_CONFIG['DbPool']['default'];
        parent::__construct($poolConfig['min'], $poolConfig['max'], $poolConfig['idleTime']);
    }

    function createObj(): \PDO
    {
        // TODO: Implement createObj() method.
        global $GLOBAL_CONFIG;
        $config = $GLOBAL_CONFIG['DB']['default'];
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
        $pdo = new \PDO($dsn, $config['username'], $config['password']);
        $pdo->exec("SET NAMES {$config['charset']}");
        return $pdo;
    }
}
