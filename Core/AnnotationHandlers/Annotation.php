<?php

namespace Core\AnnotationHandlers;

use Core\Annotations\Bean;
use Core\Annotations\Value;
use DI\Container;

return [
    //类注解
    Bean::class => function (object $instance, Container $container, Bean $annotation) {
        $vars = get_object_vars($annotation);
        if (isset($vars['name']) && $vars['name']) {
            //自定义名称
            $container->set($vars['name'], $instance);
        } else {
            //短名称
            $arr = explode("\\", get_class($instance));
            $container->set(end($arr), $instance);
        }
    },
    //属性注解
    Value::class => function (\ReflectionProperty $property, object $instance, Value $annotation) {
        $env = parse_ini_file(ROOT_PATH . '/env');
        if (!isset($env[$annotation->name]) || !$annotation->name) {
            return $instance;
        }
        $property->setValue($instance, $env[$annotation->name]);
        return $instance;
    },
];
