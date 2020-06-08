<?php

namespace Core\Lib;

use Core\BeanFactory;
use Core\Init\PHPRedisPool;

/**
 * Class RedisHelper
 * @method static string get(string $key)
 * @method static bool set(string $key, string $value, ? $params = [])
 * @method static bool setex(string $key, int $expriex, string $data)
 * @method static array hGetAll(string $key)
 * @method static bool hMset(string $key, array $data)
 * @method static int hIncrBy(string $key, string $hashKey, int $value)
 * @method static void watch(string $key)
 * @method static int del(string $key1, string $key2 = null, string $key3 = null)
 */
class RedisHelper
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        /** @var $pool PHPRedisPool */
        $pool = BeanFactory::getBean(PHPRedisPool::class);
        $redis_obj = $pool->getConnection();
        try {
            if (!$redis_obj) {
                throw new \Exception('redis pool is empty');
            }
            $redis = $redis_obj->redis;
            return $redis->$name(...$arguments);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        } finally {
            if ($redis_obj) {
                $pool->close($redis_obj);
            }
        }
    }
}