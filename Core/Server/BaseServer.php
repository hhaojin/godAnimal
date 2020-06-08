<?php

namespace Core\Server;

class BaseServer
{
    protected $server;
    protected $serverType;

    protected $fd;
    protected $pidFile;

    public function getServer()
    {
        return $this->server;
    }
}