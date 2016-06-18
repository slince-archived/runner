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

    /**
     * 测试结果
     * @var bool
     */
    protected $executedResult;

    /**
     * 写报告
     * @param $name
     * @param $value
     */
    function write($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * 读报告
     * @param $name
     * @return mixed|null
     */
    function read($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    public function setExecutedResult($executedResult)
    {
        $this->executedResult = $executedResult;
    }

    public function getExecutedResult()
    {
        return $this->executedResult;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}