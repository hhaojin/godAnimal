<?php

namespace Core\Init;

use Core\Annotations\Bean;
use Core\Annotations\Db;

/**
 * Class Controller
 * @Bean()
 * @package Core\Init
 */
class Controller
{

    /**
     * @Db(name="default")
     * @var Database
     */
    public $db;

    /**
     * @Db(name="default")
     * @var PHPRedisPool
     */
    public $redis;

}
