<?php

namespace Core\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Lock
{
    public $key = 'lock';
    public $prefix = '';
    public $locktime = 5; //锁生成时间：秒
    public $retry = 0; //抢占锁尝试次数  3次  value + 1, 0代表无限重试
    public $sleep = 100;//每次抢占间隔 ：毫秒
}
