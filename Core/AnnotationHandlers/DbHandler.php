<?php

namespace Core\AnnotationHandlers;

use Core\Annotations\Db;
use Core\BeanFactory;
use Core\Init\Database;

return [
    //属性注解
    Db::class => function (\ReflectionProperty $property, object $instance, Db $annotation) {
        /**
         * @var Database $bean
         */
        $bean = null;
        if ($annotation->name !== 'default') {
            //如果不是默认数据库
            $beanName = Database::class . "_" . $annotation->name;
            $bean = BeanFactory::getBean($beanName);
            if (!$bean) {
                $bean = clone BeanFactory::getBean(Database::class);
                $bean->setDbSource($annotation->name);
                BeanFactory::setBean($beanName, $bean);
            } else {
                $bean = clone BeanFactory::getBean($beanName);
            }
        } else {
            $bean = clone BeanFactory::getBean(Database::class);
        }
        $bean->setDbSource($annotation->name);
        $property->setAccessible(true);
        $property->setValue($instance, $bean);
        return $instance;
    },
];
