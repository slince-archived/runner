<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/18
 * Time: 11:33
 */

namespace Slince\Runner\Assertion;

use GuzzleHttp\Psr7\Response;

abstract class AbstractAssertion implements AssertionInterface
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * 设置response
     * @param Response $response
     */
    function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * 获取response
     * @return Response
     */
    function getResponse()
    {
        return $this->response;
    }
}