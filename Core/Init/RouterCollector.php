<?php

namespace Core\Init;

use Core\Annotations\Bean;
use FastRoute\RouteCollector;

/**
 * @Bean(name="RouterCollector")
 */
class RouterCollector
{
    public $routers = [];

    public function setRouter($uri, $method, $handler)
    {
        $this->routers[] = [
            'uri' => $uri,
            'handler' => $handler,
            'method' => $method,
        ];
    }

    public function getDispatcher()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routers as $router) {
                $r->addRoute($router['method'], $router['uri'], $router['handler']);
            }
        });
        return $dispatcher;
    }
}