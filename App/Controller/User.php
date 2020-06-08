<?php

namespace App\Controller;

use Core\Annotations\Bean;
use Core\Annotations\RequestMapping;
use Core\Annotations\Value;
use Core\Http\Request;
use Core\Http\Response;
use Core\Init\Controller;

/**
 * @Bean(name="User")
 */
class User extends Controller
{

    /**
     * @Value(name="version")
     */
    public $version = 1;

    /**
     * @RequestMapping(value="/testdb")
     * @param Response $response
     */
    public function testdb(Response $response)
    {
        $res = $this->db->select("select sleep(10)");

        $response->writeJson($res);
    }


    /**
     * @RequestMapping(value="/pdo")
     * @param Response $response
     */
    public function pdo(Response $response)
    {
        $res = $this->db->table("user")->first();
        $response->writeJson($res);
    }

    /**
     * @RequestMapping(value="/redirect")
     * @param Response $response
     */
    public function redirect(Response $response)
    {
        $response->redirect('https://www.baidu.com');
    }

    /**
     * @RequestMapping(value="/user/{id:\d+}")
     * @param int $id
     * @param Response $response
     */
    public function user($id, Response $response)
    {
        $response->writeJson([
            'id' => $id
        ]);
    }


    /**
     * @RequestMapping(value="/ttt")
     * @param Response $response
     */
    public function ttt(Response $response)
    {
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
        $response->writeJson('ttt');
    }

    /**
     * @RequestMapping(value="/yyy")
     * @param Response $response
     */
    public function yyy(Response $response)
    {
        $i = rand(0, 999);
        var_dump("start {$i} :" . date("Y-m-d H:i:s"));
        sleep(10);
        var_dump("end {$i} :" . date("Y-m-d H:i:s"));

        $response->writeJson('yyy');
    }

    /**
     * @RequestMapping(value="/model")
     * @param Response $response
     */
    public function model(Response $response)
    {
        try {
            $userModel = new \App\Model\User();
            $res = $userModel->find(21);
            $response->writeJson($res);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

}
