<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

class Examination
{
    /**
     * 测试id
     * @var string
     */
    protected $id;

    /**
     * @var Api
     */
    protected $api;

    /**
     * @var array
     */
    protected $assertions = [];

    /**
     * 依赖的测试
     * @var Examination
     */
    protected $dependency;

    /**
     * 是否执行过
     * @var boolean
     */
    protected $isExecuted;

    /**
     * @var Report
     */
    protected $report;

    function __construct($api)
    {
        $this->api = $api;
        $this->report = new Report();
    }

    /**
     * @return Api
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @return Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param boolean $isExecuted
     */
    public function setIsExecuted($isExecuted)
    {
        $this->isExecuted = $isExecuted;
    }
}