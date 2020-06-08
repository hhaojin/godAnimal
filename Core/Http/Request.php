<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2019/7/29
 * Time: 21:15
 */

namespace Core\Http;

use Swoole\Http\Request as swooleRequest;

class Request
{
    protected $server = [];
    protected $uri;
    protected $queryParams;
    protected $postParams;
    protected $method;
    protected $header = [];
    protected $body;
    protected $swooleRequest;

    /**
     * Request constructor.
     * @param array $server
     * @param $uri
     * @param $queryParams
     * @param $postParams
     * @param $method
     * @param array $header
     * @param $body
     */
    public function __construct(array $server, $uri, $queryParams, $postParams, $method, array $header, $body)
    {
        $this->server = $server;
        $this->uri = $uri;
        $this->queryParams = $queryParams;
        $this->postParams = $postParams;
        $this->method = $method;
        $this->header = $header;
        $this->body = $body;
    }


    static function _init(swooleRequest $swooleRequest)
    {
        $server = $swooleRequest->server;
        $method = $swooleRequest->server['request_method'] ?? 'GET';
        $uri = $server['request_uri'];
        $body = $swooleRequest->rawContent();

        $request = new self($server, $uri, $swooleRequest->get, $swooleRequest->post, $method, $swooleRequest->header, $body);
        $request->swooleRequest = $swooleRequest;
        return $request;
    }

    function getMethod()
    {
        return $this->method;
    }

    function getUri()
    {
        return $this->uri;
    }
}