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

    function execute(Response $response)
    {
        $this->response = $response;
    }

    function getResponse()
    {
        return $this->response;
    }
}