<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Assertion;

use GuzzleHttp\Psr7\Response;

interface AssertionInterface
{
    /**
     * 设置response
     * @param Response $response
     * @return mixed
     */
    function setResponse(Response $response);

    /**
     * 获取response
     * @return Response
     */
    function getResponse();
}