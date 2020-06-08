<?php

require "./vendor/autoload.php";
require "./App/Config/define.php";

use Illuminate\Database\Capsule\Manager as Capsule;

$db = new Capsule();


$db->addConnection(['driver' => 'mysql', 'database' => 'test'], 'default');
$db->setAsGlobal();
$db->bootEloquent();

$dsn = "";
{
    $driver = 'mysql';
    $host = '192.168.88.128';
    $dbname = 'test';
    $username = 'root';
    $password = '123456';
    $dsn = "$driver:host=$host;dbname=$dbname";
}
//  $dsn="mysql:host=192.168.29.1;dbname=test";
$pdo = new \PDO($dsn, $username, $password);
$db->getConnection('default')->setPdo($pdo);

$db::connection('default')->transaction(function ($db) {
    $db->table('fd_user')->insert([
        'name' => 'reSS',
        'password' => 'asdfsadfasf'
    ]);
});