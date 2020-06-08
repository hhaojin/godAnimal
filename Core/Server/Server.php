<?php

namespace Core\Server;

use Core\BeanFactory;
use Core\Init\RouterCollector;
use Core\Init\PdoPool;
use Core\Process\HotUpdate;
use FastRoute\Dispatcher;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as httpServer;
use Swoole\Process;
use Swoole\Server as swooleServer;

class Server extends BaseServer
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct()
    {
        $this->pidFile = ROOT_PATH . "/Pid/pid.pid";
    }

    public function run($arg)
    {
        $this->server = new httpServer('0.0.0.0', 7070);
        $this->server->on('Start', [$this, "onStart"]);
        $this->server->on('ManagerStart', [$this, "onManagerStart"]);
        $this->server->on('WorkerStart', [$this, "onWorkerStart"]);
        $this->server->on('Shutdown', [$this, "onShutdown"]);
        $this->server->on('request', [$this, "onRequest"]);

        $this->server->addProcess((new HotUpdate())->run());
        $this->server->set([
            'worker_num' => 2,
            'daemonize' => isset($arg[2]) ? ($arg[2] ? 1 : 0) : 0,
        ]);

        $this->echoLogo();
        echo PHP_EOL;
        echo "神兽框架启动......" . PHP_EOL;
        $this->server->start();
    }

    public function onStart(swooleServer $server)
    {
        cli_set_process_title('godAnimalMaster');
        $masterId = $server->master_pid;
        file_put_contents($this->pidFile, $masterId);
    }

    public function onWorkerStart(swooleServer $server, $workerId)
    {
        var_dump("worker $workerId start");
        cli_set_process_title('godAnimalWorker' . $workerId);

        try {
            BeanFactory::_init();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->dispatcher = BeanFactory::getBean(RouterCollector::class)->getDispatcher();
    }

    public function onManagerStart(swooleServer $server)
    {
        cli_set_process_title('godAnimalManager');
    }

    public function shutdown()
    {
        if (file_exists($this->pidFile)) {
            $pid = file_get_contents($this->pidFile);
            Process::kill($pid, SIGTERM);
            @unlink($this->pidFile);
        } else {
            var_dump("not found pid file");
        }
    }

    public function reload()
    {
        if (file_exists($this->pidFile)) {
            $pid = file_get_contents($this->pidFile);
            Process::kill($pid, SIGUSR1);
        } else {
            var_dump("not found pid file");
        }
    }

    public function onShutdown(swooleServer $server)
    {
        $pidFile = ROOT_PATH . "/Pid/pid.pid";
        @unlink($pidFile);
        var_dump("神兽框架挂逼了");
    }

    public function onRequest(Request $request, Response $response)
    {
        $myRequest = \Core\Http\Request::_init($request);
        $myResponse = \Core\Http\Response::_init($response);

        $routeInfo = $this->dispatcher->dispatch($myRequest->getMethod(), $myRequest->getUri());
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                $myResponse->setStatus(404);
                $myResponse->write("not found router");
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                // ... 405 Method Not Allowed
                $myResponse->setStatus(405);
                $myResponse->write("Method Not Allowed");
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars
                $extVars = [$myRequest, $myResponse];
                try {
                    $result = $handler($vars, $extVars);
                    if ($result) {
                        $myResponse->writeJson($result);
                    }
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                }
                break;
        }
        $myResponse->end();
    }

    public function echoLogo()
    {
        echo <<<LOGO
                                                     __----~~~~~~~~~~~------___
                                    .  .   ~~//====......          __--~ ~~
                    -.            \_|//     |||\\  ~~~~~~::::... /~
                 ___-==_       _-~o~  \/    |||  \\            _/~~-
         __---~~~.==~||\=_    -_--~/_-~|-   |\\   \\        _/~
     _-~~     .=~    |  \\-_    '-~7  /-   /  ||    \      /
   .~       .~       |   \\ -_    /  /-   /   ||      \   /
  /  ____  /         |     \\ ~-_/  /|- _/   .||       \ /
  |~~    ~~|--~~~~--_ \     ~==-/   | \~--===~~        .\
           '         ~-|      /|    |-~\~~       __--~~
                       |-~~-_/ |    |   ~\_   _-~            /\
                            /  \     \__   \/~                \__
                        _--~ _/ | .-~~____--~-/                  ~~==.
                       ((->/~   '.|||' -_|    ~~-/ ,              . _||
                                  -_     ~\      ~~---l__i__i__i--~~_/
                                  _-~-__   ~)  \--______________--~~
                                //.-~~~-~_--~- |-------~~~~~~~~
                                       //.-~~~--\
                                神兽保佑
                               代码无BUG!
LOGO;
    }

}
