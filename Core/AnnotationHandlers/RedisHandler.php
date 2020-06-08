<?php

namespace Core\AnnotationHandlers;

use Core\Annotations\RedisMapping;
use Core\BeanFactory;
use Core\Init\DecoratorCollector;
use Core\Lib\RedisHelper;

return [
    RedisMapping::class => function (\ReflectionMethod $reflectionMethod, object $instance, RedisMapping $redisMapping) {
        $collector = BeanFactory::getBean(DecoratorCollector::class);
        $key = get_class($instance) . "::" . $reflectionMethod->getName();
        $collector->dSet[$key] = function ($func) use ($redisMapping) {
            return function ($params) use ($func, $redisMapping) {
                if ($redisMapping->key) {
                    try {
                        $_key = $redisMapping->prefix . getKey($redisMapping->key, $params);
                        switch ($redisMapping->type) {
                            case "string":
                                return redisByString($_key, $redisMapping, $params, $func);
                            case "hash":
                                return redisByHash($_key, $redisMapping, $params, $func);
                            default:
                                return call_user_func($func, ...$params);
                        }
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                }
                return call_user_func($func, ...$params);
            };
        };
        return $instance;
    }
];

function getKey($key, $params)
{
    $pattern = "/^#(\d+)/i";
    if (preg_match($pattern, $key, $matches)) {
        return $params[$matches[1]];
    }
    return $key;
}

//string 类型
function redisByString(string $_key, RedisMapping $redisMapping, array $params, callable $func)
{
    $getData = RedisHelper::get($_key);
    if ($getData) {
        //缓存如果有，直接返回
        return $getData;
    } else {
        //缓存没有，则直接执行原控制器方法，并返回
        $getData = call_user_func($func, ...$params);
        if ($getData) {
            if ($redisMapping->expries > 0) {
                RedisHelper::setex($_key, (int)$redisMapping->expries, json_encode($getData, true));
            } else {
                RedisHelper::set($_key, json_encode($getData, true));
            }
        }
        return $getData;
    }
}

//哈希类型
function redisByHash(string $_key, RedisMapping $redisMapping, array $params, callable $func)
{
    if ($redisMapping->incryKey) {
        RedisHelper::hIncrBy($_key, $redisMapping->incryKey, 1);
    }
    $getData = RedisHelper::hGetAll($_key);
    if ($getData) {
        return $getData;
    } else {
        $getData = call_user_func($func, ...$params);
        if (is_object($getData)) {
            $getData = json_decode(json_encode($getData, true), true);
        }
        if (!is_array($getData)) {
            throw new \Exception("data must be array");
        }
        RedisHelper::hMset($_key, $getData);
        return $getData;
    }
}
