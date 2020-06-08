<?php

namespace Core\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class RedisMapping
{
    public $source = 'default';
    public $type = '';
    public $prefix = '';
    public $key = '';
    public $expries = 0;
    public $incryKey = 0; //hash自增
}
