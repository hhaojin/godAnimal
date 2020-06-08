<?php

namespace Core\Model;

use Core\BeanFactory;
use Core\Init\Database;
use \Illuminate\Database\Eloquent\Model;

/**
 * Class DBModel
 * @package Core\Model
 * @method mixed find(int $id)
 * @method self where(string $filed, string $op, string $value)
 * @method object get()
 */
class DBModel extends Model
{
    public $timestamps = false;

    public function __call($method, $parameters)
    {
        return $this->invoke(function () use ($method, $parameters) {
            // TODO: Change the autogenerated stub
            return parent::__call($method, $parameters);
        });
    }

    public function save(array $options = [])
    {
        return $this->invoke(function () use ($options) {
            // TODO: Change the autogenerated stub
            return parent::save($options);
        });
    }

    public function update(array $attributes = [], array $options = [])
    {
        return $this->invoke(function () use ($attributes, $options) {
            // TODO: Change the autogenerated stub
            return parent::update($attributes, $options);
        });
    }


    public function delete()
    {
        return $this->invoke(function () {
            return parent::delete(); // TODO: Change the autogenerated stub
        });
    }

    private function invoke(callable $function)
    {
        try {
            /**
             * @var Database $db
             */
            $db = clone BeanFactory::getBean(DataBase::class);
            $pdoObj = $db->genConnection();
            return $function();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $db->releaseConnection($pdoObj);
        }
    }
}