<?php

namespace Core\AnnotationHandlers;

use Core\Annotations\Lock;
use Core\Annotations\RedisMapping;
use Core\BeanFactory;
use Core\Init\DecoratorCollector;
use Core\Lib\RedisHelper;

return [
    Lock::class => function (\ReflectionMethod $reflectionMethod, object $instance, Lock $lock) {
        $collector = BeanFactory::getBean(DecoratorCollector::class);
        $key = get_class($instance) . "::" . $reflectionMethod->getName();
        $collector->dSet[$key] = function ($func) use ($lock) {
            try {
                return function ($params) use ($func, $lock) {
                    try {
                        $key = $lock->prefix . $lock->key;
                        $retry = 0;//这里争抢锁
                        while (!RedisHelper::set($key, 1, ['NX', 'EX' => $lock->locktime])) {
                            usleep($lock->sleep * 1000);
                            if ($lock->retry) {
                                $retry++;
                                if ($retry > $lock->retry) {
                                    throw new \Exception("NOT LOCK");
                                }
                            }
                        }
                        RedisHelper::watch($key);
                        $data = call_user_func($func, ...$params);
                        RedisHelper::del($key);
                        return $data;
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                };
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

        };
        return $instance;
    }
];
