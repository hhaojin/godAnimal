<?php

namespace Core\AnnotationHandlers;

use Core\Annotations\RequestMapping;
use Core\BeanFactory;
use Core\Init\DecoratorCollector;
use Core\Init\RouterCollector;

return [
    RequestMapping::class => function (\ReflectionMethod $reflectionMethod, object $instance, RequestMapping $RequestMapping) {

        $method = $RequestMapping->method ?: 'GET';
        $RouterCollector = BeanFactory::getBean(RouterCollector::class);
        if ($RouterCollector instanceof RouterCollector) {
            $RouterCollector->setRouter($RequestMapping->value, $method, function ($vars, $extVars) use (
                $reflectionMethod,
                $instance
            ) {
                $params = $reflectionMethod->getParameters(); //获取反射参数
                $uriParams = [];
                foreach ($params as $param) {
                    if (isset($vars[$param->getName()])) {
                        $uriParams[$param->getName()] = $vars[$param->getName()];
                    } else {
                        foreach ($extVars as $extVar) {
                            if ($param->getClass()) {
                                if ($param->getClass()->isInstance($extVar)) {
                                    $uriParams[$param->getName()] = $extVar;
                                    goto end;
                                }
                            }
                        }
                        $uriParams[$param->getName()] = null;

                        end:
                        continue;
                    }
                }

                try {
                    /** @var DecoratorCollector $cllo */
                    $cllo = BeanFactory::getBean(DecoratorCollector::class);
                    return $cllo->exec($reflectionMethod, $instance, array_values($uriParams));
//                return $reflectionMethod->invokeArgs($instance, $uriParams);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }

            });
        }

        return $instance;
    }
];
