<?php

namespace App\Controller;

use Core\Annotations\Bean;
use Core\Annotations\Lock;
use Core\Annotations\RedisMapping;
use Core\Annotations\RequestMapping;
use Core\Http\Request;
use Core\Http\Response;
use Core\Init\Controller;

/**
 * @Bean(name="Test")
 */
class Test extends Controller
{
    /**
     * @RedisMapping(key="#2",prefix="huser_",type="hash",incryKey="mobile")
     * @RequestMapping(value="/testredis/{id:\d+}")
     * @param Request $request
     * @param Response $response
     * @param int $id
     * @return
     */
    public function testredis(Request $request, Response $response, $id)
    {
        $userModel = new \App\Model\User();

        $res = $userModel->where('id', '=', $id)->get();
        return $res;
    }


    /**
     * @Lock(prefix="goods",key="lock")
     * @RequestMapping(value="/testlock1")
     * @param Request $request
     * @param Response $response
     */
    public function testlock1(Request $request,Response $response)
    {
        sleep(10);
        return 1;
    }

    /**
     * @Lock(prefix="goods",key="lock",retry="5")
     * @RequestMapping(value="/testlock2")
     * @param Request $request
     * @param Response $response
     */
    public function testlock2(Request $request,Response $response)
    {
        return 2;
    }
}
