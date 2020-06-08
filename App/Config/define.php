<?php

define('ROOT_PATH', dirname(dirname(__DIR__)));

$GLOBAL_CONFIG = [
    'DB' => require (ROOT_PATH . "/App/Config/db.php"),
    'DbPool' => require (ROOT_PATH . "/App/Config/dbpool.php"),
    'Redis' => require (ROOT_PATH . "/App/Config/redis.php"),
    'Redispool' => require (ROOT_PATH . "/App/Config/redispool.php"),
];
