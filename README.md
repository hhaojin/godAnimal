# swoole_framework

#### 介绍
基于swoole4.4.x写的http server框架，框架引用了注解的方式，实现了Ioc容器，协程连接池，自动回收连接，ORM，热更新等。。。  （纯属个人娱乐行为，请勿用于生产）

#### 软件架构
```
App--   应用目录
    |--Config   配置目录
        |--db.php   数据库配置，
        |--dbpool.php  数据库连接池配置
        |--redis.php  redis配置
        |--reidspool.php  redis连接池配置
    |--Controller  控制器
    |--Model
Core--      框架核心
    |--AnnotationHandler   注解处理类
    |--Annotations      注解类
    |--Helper   助手函数
    |--Http     http 相关
    |--Init     初始化
    |--Lib      公共类库，连接池
    |--Model    ORM方法实现
    |--Process  子进程
    |--Server   swoole服务
Pid--   pid保存目录
    |
Process--   用户定义进程
    |           
    
```


#### 安装教程

1.   git clone xxxxx
2.   composer install

#### 使用说明

```
运行： php godAnimal start

框架启动会扫描App目录，所有的类必须打上@Bean()注解，才会处理类的属性，方法上的注解，否则无效，同时类会注入BeanFactory，统一管理，支持短名称
```
```
路由 : hello world
<?php
/**
 * @Bean()
 */
class Index extends Controller
{
    /**
     * @RequestMapping(value="/")
     */
    public function Index(Response $response)
    {
        $response->write("hello world");
    }
}
```

```
bean容器
\Core\BeanFactory::class
\Core\BeanFactory::getBean()  //获取类
\Core\BeanFactory::setBean()  //注入类
\Core\BeanFactory::getEnv()   //获取env配置
等等
```

```
redis缓存注解
/**
 * @RedisMapping(key="#2",prefix="huser_",type="string",expire=10)
 */
public function testredis(Request $request, Response $response, $id)
{
    return "cache";
}

```

```
redis分布式锁
/**
 * @Lock(prefix="goods",key="lock",retry="5")
 */
```

```
Value注解, 会注入env文件里面的值，赋值给属性
<?php
/**
 * @Bean()
 */
class test {
    
    /**
     * @Value(name="index")
     */
    public $a;
}
```

```
DB注解
<?php
/**
 * Class Controller
 * @Bean()
 */
class Controller
{
    /**
     * @Db(name="test")
     * @var Database
     */
    public $db;

    /**
     * @Db(name="default")
     * @var PHPRedisPool
     */
    public $redis;

}
```

```
事务
    try {
        $db = $this->db->begin();

        $db->table('user')->insert([
            'name' => 'ttt' . rand(1, 9999999),
            'password' => 'asdf',
        ]);

        $db->table('user')->insert([
            'name' => 'ttt' . rand(1, 9999999),
            'password' => 'asdf',
        ]);

        $db->commit();
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
```
