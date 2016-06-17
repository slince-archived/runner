<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

class Report
{
    /**
     * 参数
     * @var array
     */
    protected $parameters = [];

    function write($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    function read($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }
}