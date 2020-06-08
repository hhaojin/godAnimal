<?php

namespace Core\Lib;

use Swoole\Coroutine\Channel;
use Swoole\Timer;

/**
 * Class DbPool
 * @package Core\Init
 */
abstract class DbPool
{
    private $connections;
    private $min;
    private $max;
    private $idleTime = 10;
    private $connectCount;

    abstract function createObj();

    public function __construct($min = 5, $max = 10, $idleTime = 10)
    {
        $this->min = $min;
        $this->max = $max;
        $this->idleTime = $idleTime;
        $this->connections = new Channel($max);

        //初始化连接池
        for ($i = 0; $i < $this->min; $i++) {
            if ($this->connectCount < $this->max) {
                $this->addObjToPool();
            } else {
                break;
            }
        }
    }

    /**
     * 初始化连接池
     */
    public function _init()
    {
        //定时清除空闲连接数
//        Timer::tick(2000, function () {
//            $length = $this->connections->length();
//            $time = time() - $this->idleTime;
//
//            for ($i = 0; $i < $length; $i++) {
//                if ($this->connections->isEmpty()) {
//                    continue;
//                }
//                $obj = $this->connections->pop(0.1);
//                if ($obj->time >= $time) {
//                    $this->connections->push($obj);
//                } else if ($obj->time < $time && $this->connectCount <= $this->min) {
//                    $this->connections->push($obj);
//                } else {
//                    $this->connectCount--;
//                }
//            }
//        });
    }

    /**
     * 获取一个连接池对象
     * @return mixed
     */
    public function getObj()
    {
        if ($this->connections->isEmpty() && $this->connectCount < $this->max) {
            $this->addObjToPool();
        }

        return $this->connections->pop(0.1);
    }

    /**
     * 回收连接
     * @param $obj
     */
    public function recycleObj($obj)
    {
        if ($this->connectCount < $this->max) {
            $obj->time = time(); //记录回收时间
            $this->connections->push($obj);
        }
    }

    /**
     * 往连接池里新增一个对象
     */
    private function addObjToPool()
    {
        try {
            $this->connectCount++;
            $obj = $this->createObj(); //此处会发生协程切换

            if (!$obj) {
                throw new \Exception('创建DB连接失败');
            }

            $stdClass = new \stdClass();
            $stdClass->time = time();
            $stdClass->db = $obj;
            $this->connections->push($stdClass);

        } catch (\Exception $e) {
            $this->connectCount--;
            var_dump('addObj->' . $e->getMessage());
        }
    }

    public function getConnectCount()
    {
        return $this->connectCount;
    }
}
