<?php

namespace Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Value
{
    public $name;

    public function parseIni()
    {
        $ini = parse_ini_file(ROOT_PATH . '/env');
        if (isset($ini[$this->name])) {
            return $ini[$this->name];
        }
        return false;
    }
}
