<?php

namespace Core\Init;

use Core\Annotations\Bean;

/**
 * Class DecoratorCollector 装饰器收集器
 * @package Core\Init
 * @Bean()
 */
class DecoratorCollector
{
    // class::method => closure
    public $dSet = [];

    public function exec(\ReflectionMethod $reflectionMethod, object $instance, $params)
    {
        $key = get_class($instance) . "::" . $reflectionMethod->getName();
        if (isset($this->dSet[$key])) {
            $func = $this->dSet[$key];
            return $func($reflectionMethod->getClosure($instance))($params);
        }

        return $reflectionMethod->invokeArgs($instance, $params);
    }
}