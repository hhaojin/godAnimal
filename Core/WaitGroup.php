<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2019/7/8
 * Time: 11:30
 */

namespace Core;

use Swoole\Coroutine as co;

/**
 * 使协程阻塞化
 */
class WaitGroup
{
    private $chan;
    private $count = 0;

    function __construct($count)
    {
        $this->chan = new co\Channel();
        $this->count = $count;
    }

    public function addCount($count): void
    {
        $this->count += $count;
    }

    //执行弹出等待
    public function wait(): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            $this->chan->pop();
        }
    }

    public function done(): void
    {
        $this->chan->push(1);
    }

    public function close(): void
    {
        $this->chan->close();
    }


}

