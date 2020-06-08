<?php

namespace Core\Init;

use Core\Annotations\Bean;
use Core\BeanFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Class Database
 * @Bean
 * @method \Illuminate\Database\Query\Builder table(string $table, string|null $connection = null)
 * @method \Illuminate\Database\Query\Builder select(array|mixed $columns)
 */
class Database
{
    /**
     * @var Capsule
     */
    private $lvdb;

    private $dbSource = 'default';

    /**
     * @var PdoPool
     */
    public $pdoPool;

    /**
     * @var object stdClass
     */
    private $transactionDb = false;

    public function __construct($dbObj = false)
    {
        global $GLOBAL_CONFIG;
        if (isset($GLOBAL_CONFIG['DB'])) {
            $this->lvdb = new Capsule();
            $config = $GLOBAL_CONFIG['DB'];
            foreach ($config as $key => $value) {
                $this->lvdb->addConnection([
                    'driver' => 'mysql',
                    'database' => $value['database'],
                    'prefix' => $value['prefix'],
                    'charset' => $value['charset'],
                    'collation' => $value['collation'],
                ], $key);
            }
            $this->lvdb->setAsGlobal();
            $this->lvdb->bootEloquent();
        }
        $this->transactionDb = $dbObj;
        $this->pdoPool = BeanFactory::getBean(PdoPool::class);

        if ($dbObj) {
            //是否开启事务
            $this->lvdb->getConnection($this->dbSource)->setPdo($this->transactionDb->db);
            $this->lvdb->getConnection($this->dbSource)->beginTransaction();
        }
    }

    public function __call($name, $args)
    {
        $isTransaction = false;
        if ($this->transactionDb) {
            //判断是否开启事务
            $pdoObj = $this->transactionDb;
            $isTransaction = true;
        } else {
            $pdoObj = $this->pdoPool->getObj();
        }
        try {
            if (!$pdoObj) {
                throw new \Exception('pdo pool is empty');
            }
            if (!$isTransaction) {
                $this->lvdb->getConnection($this->dbSource)->setPdo($pdoObj->db);
            }
            $res = $this->lvdb::connection($this->dbSource)->$name(...$args);
            return $res;
        } catch (\Exception $e) {
            return $e->getMessage();
        } finally {
            if ($pdoObj && !$isTransaction) {
                $this->pdoPool->recycleObj($pdoObj);
            }
        }
    }


    /**
     * 开启事务
     */
    public function begin(): self
    {
        $obj = $this->pdoPool->getObj();
        if (!$obj) {
            throw new \Exception('begin pdo pool is empty');
        }

        return new self($obj);
    }

    /**
     * 提交事务
     */
    public function commit(): void
    {
        if ($this->transactionDb) {
            try {
                $this->lvdb->getconnection($this->dbSource)->commit();
            } catch (\Exception $e) {
                throw new \Exception("transaction commit:" . $e->getMessage());
            } finally {
                $this->pdoPool->recycleObj($this->transactionDb);
                $this->transactionDb = false;
            }
        }
    }

    /**
     * 回滚事务
     */
    public function rollback(): void
    {
        if ($this->transactionDb) {
            try {
                $this->lvdb->getconnection($this->dbSource)->rollBack();
            } catch (\Exception $e) {
                throw new \Exception('transaction rollback:' . $e->getMessage());
            } finally {
                $this->pdoPool->recycleObj($this->transactionDb);
                $this->transactionDb = false;
            }
        }
    }


    /**
     * @return string
     */
    public function getDbSource(): string
    {


        return $this->dbSource;
    }

    /**
     * @param string $dbSource
     */
    public function setDbSource(string $dbSource): void
    {
        $this->dbSource = $dbSource;
    }


    /**
     * 设置一个连接,给model用的
     * @return bool|mixed|object
     * @throws \Exception
     */
    public function genConnection()
    {
        $isTransaction = false;
        if ($this->transactionDb) {
            //判断是否开启事务
            $pdoObj = $this->transactionDb;
            $isTransaction = true;
        } else {
            $pdoObj = $this->pdoPool->getObj();
        }
        try {
            if (!$pdoObj) {
                throw new \Exception('pdo pool is empty');
            }
            if (!$isTransaction) {
                $this->lvdb->getConnection($this->dbSource)->setPdo($pdoObj->db);
                return $pdoObj;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return false;
    }

    /**
     * 回收连接，model用
     * @param object $dbObj
     */
    public function releaseConnection($dbObj): void
    {
        if ($dbObj && !$this->transactionDb) {
            $this->pdoPool->recycleObj($dbObj);
        }
    }
}
