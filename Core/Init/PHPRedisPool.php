<?php

namespace Core\Init;

use Core\annotations\Bean;
use Core\lib\RedisPool;

/**
 * Class PHPRedisPool
 * @Bean()
 */
class PHPRedisPool extends RedisPool
{

    public function __construct(int $min = 5, int $max = 10)
    {
        global $GLOBAL_CONFIG;
        $poolconfig = $GLOBAL_CONFIG["Redispool"]["default"];

        parent::__construct($poolconfig['min'], $poolconfig['max'], $poolconfig['idleTime']);
    }

    protected function newRedis()
    {
        global $GLOBAL_CONFIG;
        $default = $GLOBAL_CONFIG["Redis"]["default"];

        $redis = new \Redis();
        $redis->connect($default["host"], $default["port"]);
        if ($default["auth"]) {
            $redis->auth($default["auth"]);
        }
        return $redis;
    }
}
