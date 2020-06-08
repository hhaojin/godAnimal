<?php

namespace Core\Lib;

abstract class  RedisPool
{
    private $min;
    private $max;
    private $conns;
    private $count;//当前所有连接数
    private $idleTime = 10;//连接空闲时间秒

    abstract protected function newRedis();

    function __construct($min = 5, $max = 10, $idleTime = 10)
    {
        $this->min = $min;
        $this->max = $max;
        $this->idleTime = $idleTime;
        $this->conns = new \Swoole\Coroutine\Channel($this->max);
        //构造方法直接初始化Redis连接
        for ($i = 0; $i < $this->min; $i++) {
            $this->addRedisToPool();//统一调用
        }
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getConnection()
    {
        //取出
        if ($this->conns->isEmpty()) {
            if ($this->count < $this->max) {//连接池没满
                $this->addRedisToPool();
                $getObject = $this->conns->pop();
            } else {
                $getObject = $this->conns->pop(0.1);
            }
        } else {
            $getObject = $this->conns->pop();
        }
        if ($getObject)
            $getObject->usedtime = time();
        return $getObject;
    }

    public function close($conn)
    {//放回连接
        if ($conn) {
            $this->conns->push($conn);
        }
    }

    public function addRedisToPool()
    { //把对象加入池
        try {
            $this->count++;
            $db = $this->newRedis();
            if (!$db) throw  new \Exception("redis创建错误");
            $dbObject = new \stdClass();
            $dbObject->usedTime = time();
            $dbObject->redis = $db;

            $this->conns->push($dbObject);
        } catch (\Exception $ex) {
            $this->count--;
        }
    }
}