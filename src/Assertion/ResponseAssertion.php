<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Assertion;


use Slince\Runner\Exception\InvalidArgumentException;

class ResponseAssertion extends AbstractAssertion
{

    function __call($name, $arguments)
    {
        if (substr($name, 0, 2) == 'is') {
            return $this->getStatusCode() == substr($name, 2);
        }
        throw new InvalidArgumentException(sprintf("Assert Method [%s] does not exist", $name));
    }

    /**
     * 获取状态码
     * @return int
     */
    protected function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * 是否ok
     * @param $result
     * @return bool
     */
    function isOk($result)
    {
        return $result == ($this->getStatusCode() == 200);
    }

    /**
     * 是否是404
     * @param $result
     * @return bool
     */
    function isNotFound($result)
    {
        return $result == ($this->getStatusCode() == 404);
    }
}