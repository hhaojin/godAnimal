<?php

namespace Core\Http;

use Swoole\Http\Response as swooleResponse;

class Response
{

    /**
     * @var swooleResponse
     */
    private $swooleResponse;
    private $body;

    public function __construct(swooleResponse $response)
    {
        $this->swooleResponse = $response;
        $this->setHeader("Content-type", "application/json;charset=utf-8");
    }

    static function _init(swooleResponse $swooleResponse)
    {
        return new self($swooleResponse);
    }

    public function writeJson($data, $msg = 'success', $code = 100000)
    {
        $json = json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ], true);

        $this->write($json);
    }

    public function writeError($code = 200000, $msg = 'error', $data = [])
    {
        $json = json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ], true);

        $this->write($json);
    }

    public function redirect($url, $httpCode = 301)
    {
        $this->setStatus($httpCode);
        $this->setHeader('Location', $url);
    }

    public function setStatus($httpCode)
    {
        $this->swooleResponse->status($httpCode);
    }

    public function setHeader($type, $content)
    {
        $this->swooleResponse->header($type, $content);
    }

    /**
     * 结束Http响应，发送HTML内容
     * @param string $html
     */
    public function end($html = '')
    {
        $this->swooleResponse->end($html);
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * 启用Http-Chunk分段向浏览器发送数据
     * @param $html
     */
    public function write($html)
    {
        $this->swooleResponse->write($html);
    }


    /**
     * 设置Cookie
     *
     * @param string $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     */
    public function cookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {

    }

}