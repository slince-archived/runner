<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Assertion;


class ResponseAssertion extends AbstractAssertion
{

    function __call($name, $arguments)
    {
        if (substr($name, 0, 2) == 'is') {
            return $this->getStatusCode() == substr($name, 2);
        }
    }

    /**
     * 获取状态码
     * @return int
     */
    function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * 是否ok
     * @return boolean
     */
    function isOk()
    {
        return $this->getStatusCode() == 200;
    }

    /**
     * 是否是404
     * @return bool
     */
    function isNotFound()
    {
        return $this->getStatusCode() == 404;
    }
}