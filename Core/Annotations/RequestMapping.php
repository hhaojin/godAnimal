<?php

namespace Core\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class RequestMapping
{
    public $value;

    public $method;
}